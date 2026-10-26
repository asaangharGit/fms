<?php

namespace App\Support\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Builds the JSON envelope shared by every API endpoint:
 *
 *   success: { "data": ..., "meta": {...}, "links": {...} }
 *   error:   { "error": { "status", "code", "message", "details"?, "request_id" } }
 */
class ApiResponse
{
    const JSON_OPTIONS = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION;

    public static function item($data, array $meta = [])
    {
        return self::json(['data' => $data, 'meta' => self::baseMeta($meta)]);
    }

    public static function collection(array $rows, array $meta = [])
    {
        return self::json([
            'data' => $rows,
            'meta' => self::baseMeta(array_merge(['count' => count($rows)], $meta)),
        ]);
    }

    /**
     * Keyset-paginated list. $nextCursor is null on the last page.
     */
    public static function paginated(Request $request, array $rows, $limit, $nextCursor, array $meta = [])
    {
        return self::json([
            'data' => $rows,
            'meta' => self::baseMeta(array_merge([
                'count' => count($rows),
                'limit' => $limit,
                'has_more' => $nextCursor !== null,
                'next_cursor' => $nextCursor,
            ], $meta)),
            'links' => [
                'self' => self::urlWithQuery($request, []),
                'next' => $nextCursor === null ? null : self::urlWithQuery($request, ['cursor' => $nextCursor]),
            ],
        ]);
    }

    public static function error(Request $request, $status, $code, $message, array $details = null, array $headers = [])
    {
        $error = [
            'status' => $status,
            'code' => $code,
            'message' => $message,
        ];
        if ($details) {
            $error['details'] = $details;
        }
        $error['request_id'] = $request->attributes->get('request_id');

        return self::json(['error' => $error], $status, $headers);
    }

    public static function json(array $payload, $status = 200, array $headers = [])
    {
        return new JsonResponse($payload, $status, $headers, self::JSON_OPTIONS);
    }

    protected static function baseMeta(array $meta)
    {
        return array_merge($meta, ['generated_at' => now()->toIso8601String()]);
    }

    /**
     * Current URL with merged query parameters. The query-string API key
     * (when that mode is enabled) is never echoed back in links.
     */
    protected static function urlWithQuery(Request $request, array $overrides)
    {
        $query = array_merge($request->query(), $overrides);
        unset($query[config('api.auth.query_key_name', 'api_key')]);
        ksort($query);

        return $request->url() . ($query ? '?' . http_build_query($query) : '');
    }
}
