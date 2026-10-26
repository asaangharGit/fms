<?php

namespace App\Console\Commands;

use App\Models\ApiClient;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\HttpFoundation\IpUtils;

class ApiClientCreate extends Command
{
    protected $signature = 'api-client:create
                            {name : Descriptive name, e.g. "Power BI - Finance"}
                            {--scope=* : Scope(s) to grant (default: gl.read)}
                            {--ip=* : Allowed IP or CIDR (repeatable; omit to allow any IP)}
                            {--expires= : Expiry date (Y-m-d). Recommended: 6-12 months}';

    protected $description = 'Issue an API key for a machine-to-machine client (e.g. Power BI)';

    public function handle()
    {
        $name = trim($this->argument('name'));
        if ($name === '' || strlen($name) > 100) {
            $this->error('Name must be 1-100 characters.');

            return 1;
        }

        $scopes = $this->option('scope') ?: ['gl.read'];
        foreach ($scopes as $scope) {
            if ($scope !== '*' && ! array_key_exists($scope, ApiClient::SCOPES)) {
                $this->error('Unknown scope "' . $scope . '". Available: ' . implode(', ', array_keys(ApiClient::SCOPES)));

                return 1;
            }
        }

        $ips = $this->option('ip');
        foreach ($ips as $ip) {
            // checkIp() against itself validates both plain IPs and CIDR notation.
            $probe = explode('/', $ip)[0];
            if (! filter_var($probe, FILTER_VALIDATE_IP) || ! IpUtils::checkIp($probe, $ip)) {
                $this->error('Invalid IP or CIDR: ' . $ip);

                return 1;
            }
        }

        $expiresAt = null;
        if ($this->option('expires')) {
            try {
                $expiresAt = Carbon::createFromFormat('Y-m-d', $this->option('expires'))->endOfDay();
            } catch (\Exception $e) {
                $this->error('Invalid --expires date. Use Y-m-d.');

                return 1;
            }
            if ($expiresAt->isPast()) {
                $this->error('--expires must be in the future.');

                return 1;
            }
        } else {
            $this->warn('No --expires given: the key will not expire. Rotate it periodically.');
        }

        list($client, $plainKey) = ApiClient::issue($name, $scopes, $ips, $expiresAt);

        $this->info('API client created.');
        $this->table(['ID', 'Name', 'Scopes', 'Allowed IPs', 'Expires'], [[
            $client->id,
            $client->name,
            implode(', ', $client->scopes),
            $ips ? implode(', ', $ips) : 'any',
            $expiresAt ? $expiresAt->toDateTimeString() : 'never',
        ]]);
        $this->line('');
        $this->line('API key (shown ONCE - store it in a password manager / Power BI credentials):');
        $this->line('');
        $this->line('    ' . $plainKey);
        $this->line('');

        return 0;
    }
}
