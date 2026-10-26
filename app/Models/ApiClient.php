<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\IpUtils;

/**
 * Machine-to-machine API client (e.g. Power BI).
 *
 * Plain-text keys are never stored: a key looks like
 * "fms_<prefix>_<secret>" and only its SHA-256 hash is persisted. The
 * prefix is stored separately so the row can be found with an indexed
 * lookup before the hash is compared in constant time.
 *
 * @property int $id
 * @property string $name
 * @property string $key_prefix
 * @property string $key_hash
 * @property array $scopes
 * @property array|null $allowed_ips
 * @property \Carbon\Carbon|null $expires_at
 * @property \Carbon\Carbon|null $last_used_at
 * @property string|null $last_used_ip
 * @property \Carbon\Carbon|null $revoked_at
 */
class ApiClient extends Model
{
    const KEY_PREFIX = 'fms';

    /** Scopes understood by the API. "*" grants all of them. */
    const SCOPES = [
        'gl.read' => 'Read General Ledger data (accounts, vouchers, entries, reports)',
    ];

    protected $table = 'api_clients';

    protected $fillable = ['name', 'scopes', 'allowed_ips', 'expires_at'];

    protected $hidden = ['key_hash'];

    protected $casts = [
        'scopes' => 'array',
        'allowed_ips' => 'array',
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    /**
     * Create a client and return it together with its plain-text key.
     * The key is only available here; it cannot be recovered later.
     *
     * @return array [ApiClient $client, string $plainKey]
     */
    public static function issue($name, array $scopes, array $allowedIps = [], Carbon $expiresAt = null)
    {
        $prefix = Str::lower(Str::random(12));
        $secret = Str::random(48);
        $plainKey = self::KEY_PREFIX . '_' . $prefix . '_' . $secret;

        $client = new static([
            'name' => $name,
            'scopes' => array_values(array_unique($scopes)),
            'allowed_ips' => $allowedIps ? array_values($allowedIps) : null,
            'expires_at' => $expiresAt,
        ]);
        $client->key_prefix = $prefix;
        $client->key_hash = hash('sha256', $plainKey);
        $client->save();

        return [$client, $plainKey];
    }

    /**
     * Resolve a client from a plain-text key, or null when the key is
     * malformed or unknown. Does not check revocation / expiry.
     */
    public static function findByKey($plainKey)
    {
        if (! is_string($plainKey) || strlen($plainKey) > 128) {
            return null;
        }

        $parts = explode('_', $plainKey, 3);
        if (count($parts) !== 3 || $parts[0] !== self::KEY_PREFIX || $parts[1] === '' || $parts[2] === '') {
            return null;
        }

        $client = static::where('key_prefix', $parts[1])->first();

        if (! $client || ! hash_equals($client->key_hash, hash('sha256', $plainKey))) {
            return null;
        }

        return $client;
    }

    public function isRevoked()
    {
        return $this->revoked_at !== null;
    }

    public function isExpired()
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isActive()
    {
        return ! $this->isRevoked() && ! $this->isExpired();
    }

    public function hasScope($scope)
    {
        $scopes = $this->scopes ?: [];

        return in_array('*', $scopes, true) || in_array($scope, $scopes, true);
    }

    /**
     * Whether the IP is allowed. An empty allow-list allows any IP.
     * Entries may be single addresses or CIDR ranges (IPv4 / IPv6).
     */
    public function allowsIp($ip)
    {
        $allowed = $this->allowed_ips ?: [];

        return empty($allowed) || IpUtils::checkIp((string) $ip, $allowed);
    }

    public function revoke()
    {
        $this->revoked_at = $this->freshTimestamp();
        $this->save();
    }

    /**
     * Record usage, writing at most once per touch_interval seconds so
     * paginated Power BI refreshes do not cause a write per request.
     */
    public function recordUsage($ip)
    {
        $interval = (int) config('api.auth.touch_interval', 60);

        if ($this->last_used_at && $this->last_used_at->diffInSeconds(now()) < $interval && $this->last_used_ip === $ip) {
            return;
        }

        $this->forceFill(['last_used_at' => $this->freshTimestamp(), 'last_used_ip' => $ip])->saveQuietly();
    }
}
