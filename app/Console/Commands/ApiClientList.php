<?php

namespace App\Console\Commands;

use App\Models\ApiClient;
use Illuminate\Console\Command;

class ApiClientList extends Command
{
    protected $signature = 'api-client:list';

    protected $description = 'List API clients (keys are never shown)';

    public function handle()
    {
        $rows = ApiClient::orderBy('id')->get()->map(function (ApiClient $client) {
            if ($client->isRevoked()) {
                $state = 'revoked';
            } elseif ($client->isExpired()) {
                $state = 'expired';
            } else {
                $state = 'active';
            }

            return [
                $client->id,
                $client->name,
                ApiClient::KEY_PREFIX . '_' . $client->key_prefix . '_…',
                implode(', ', $client->scopes ?: []),
                $client->allowed_ips ? implode(', ', $client->allowed_ips) : 'any',
                $client->expires_at ? $client->expires_at->toDateString() : 'never',
                $client->last_used_at ? $client->last_used_at->toDateTimeString() . ' (' . $client->last_used_ip . ')' : '-',
                $state,
            ];
        })->all();

        $this->table(['ID', 'Name', 'Key', 'Scopes', 'Allowed IPs', 'Expires', 'Last used', 'State'], $rows);

        return 0;
    }
}
