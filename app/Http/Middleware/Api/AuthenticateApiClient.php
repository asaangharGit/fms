<?php

namespace App\Http\Middleware\Api;

use App\Models\ApiClient;
use App\Support\Api\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Authenticates machine-to-machine clients by API key and enforces
 * revocation, expiry, IP allow-list and scopes.
 *
 * Usage: ->middleware('api.client:gl.read')
 *
 * Every authentication failure returns the same generic 401 so callers
 * cannot tell an unknown key from a revoked or expired one. Repeated
 * failures from one IP are throttled to slow down key guessing.
 */
class AuthenticateApiClient
{
    public function handle(Request $request, Closure $next, ...$scopes)
    {
        $throttleKey = 'api-auth-failed:' . $request->ip();
        $maxAttempts = (int) config('api.auth.max_failed_attempts', 10);

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $retryAfter = RateLimiter::availableIn($throttleKey);

            return ApiResponse::error($request, 429, 'too_many_failed_attempts',
                'Too many failed authentication attempts. Retry later.', null, ['Retry-After' => $retryAfter]);
        }

        $key = $this->extractKey($request);
        $client = $key === null ? null : ApiClient::findByKey($key);

        if ($client === null || ! $client->isActive()) {
            RateLimiter::hit($throttleKey, 60);
            Log::channel('api')->warning('api.auth_failed', [
                'request_id' => $request->attributes->get('request_id'),
                'ip' => $request->ip(),
                'reason' => $key === null ? 'missing_key' : ($client === null ? 'invalid_key' : ($client->isRevoked() ? 'revoked' : 'expired')),
                'client_id' => $client ? $client->id : null,
            ]);

            return ApiResponse::error($request, 401, 'unauthenticated',
                'A valid API key is required.', null, ['WWW-Authenticate' => 'Bearer realm="fms-api"']);
        }

        if (! $client->allowsIp($request->ip())) {
            Log::channel('api')->warning('api.ip_denied', [
                'request_id' => $request->attributes->get('request_id'),
                'ip' => $request->ip(),
                'client_id' => $client->id,
            ]);

            return ApiResponse::error($request, 403, 'ip_not_allowed', 'This API key is not allowed from your IP address.');
        }

        foreach ($scopes as $scope) {
            if (! $client->hasScope($scope)) {
                return ApiResponse::error($request, 403, 'insufficient_scope', 'This API key lacks the required scope: ' . $scope . '.');
            }
        }

        $client->recordUsage($request->ip());
        $request->attributes->set('api_client', $client);

        return $next($request);
    }

    /**
     * Key from "Authorization: Bearer", "X-Api-Key", or (when enabled) the
     * query string.
     */
    protected function extractKey(Request $request)
    {
        $bearer = $request->bearerToken();
        if ($bearer) {
            return $bearer;
        }

        $header = $request->header('X-Api-Key');
        if ($header) {
            return $header;
        }

        if (config('api.auth.allow_query_key')) {
            $value = $request->query(config('api.auth.query_key_name', 'api_key'));

            return is_string($value) && $value !== '' ? $value : null;
        }

        return null;
    }
}
