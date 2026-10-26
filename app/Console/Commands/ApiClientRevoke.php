<?php

namespace App\Console\Commands;

use App\Models\ApiClient;
use Illuminate\Console\Command;

class ApiClientRevoke extends Command
{
    protected $signature = 'api-client:revoke {id : API client id (see api-client:list)}';

    protected $description = 'Revoke an API client key immediately';

    public function handle()
    {
        $client = ApiClient::find($this->argument('id'));

        if (! $client) {
            $this->error('API client not found.');

            return 1;
        }
        if ($client->isRevoked()) {
            $this->warn('Already revoked at ' . $client->revoked_at->toDateTimeString() . '.');

            return 0;
        }

        $client->revoke();
        $this->info('Revoked API client #' . $client->id . ' (' . $client->name . ').');

        return 0;
    }
}
