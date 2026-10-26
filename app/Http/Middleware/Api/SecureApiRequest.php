<?php

namespace App\Http\Middleware\Api;

use App\Support\Api\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Transport-level hardening for the machine-to-machine API:
 *
 * - assigns a request id (X-Request-Id) used in logs and error bodies
 * - forces JSON responses (validation and exceptions never render HTML)
 * - rejects plain HTTP outside local/testing
 * - adds no-store / nosniff / restrictive CSP response headers
 * - writes one structured access-log line per request (channel "api")
 */
class SecureApiRequest
{
    public function handle(Request $request, Closure $next)
    {
        $request->attributes->set('request_id', (string) Str::uuid());
        $request->attributes->set('started_at', microtime(true));
        $request->headers->set('Accept', 'application/json');

        if (config('api.require_https') && ! $request->secure() && ! app()->environment('local', 'testing')) {
            return $this->withHeaders($request, ApiResponse::error($request, 403, 'https_required', 'HTTPS is required.'));
        }

        return $this->withHeaders($request, $next($request));
    }

    public function terminate(Request $request, $response)
    {
        $client = $request->attributes->get('api_client');
        $started = $request->attributes->get('started_at', microtime(true));

        $query = $request->query();
        unset($query[config('api.auth.query_key_name', 'api_key')]);

        Log::channel('api')->info('api.request', [
            'request_id' => $request->attributes->get('request_id'),
            'client_id' => $client ? $client->id : null,
            'client' => $client ? $client->name : null,
            'ip' => $request->ip(),
            'method' => $request->method(),
            'path' => '/' . ltrim($request->path(), '/'),
            'query' => $query,
            'status' => $response->getStatusCode(),
            'duration_ms' => (int) round((microtime(true) - $started) * 1000),
        ]);
    }

    protected function withHeaders(Request $request, $response)
    {
        $headers = $response->headers;
        $headers->set('X-Request-Id', $request->attributes->get('request_id'));
        $headers->set('Cache-Control', 'no-store, private');
        $headers->set('Pragma', 'no-cache');
        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('X-Frame-Options', 'DENY');
        $headers->set('Referrer-Policy', 'no-referrer');
        $headers->set('Content-Security-Policy', "default-src 'none'; frame-ancestors 'none'");
        if ($request->secure()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000');
        }
        $headers->remove('X-Powered-By');
        if (function_exists('header_remove') && ! headers_sent()) {
            header_remove('X-Powered-By');
        }

        return $response;
    }
}
