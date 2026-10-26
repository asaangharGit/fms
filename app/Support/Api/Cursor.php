<?php

namespace App\Support\Api;

use Illuminate\Validation\ValidationException;

/**
 * Opaque keyset-pagination cursor: base64url-encoded JSON {"id": <last id>}.
 * Keyset pagination stays stable and fast on large tables, unlike OFFSET.
 */
class Cursor
{
    public static function encode($lastId)
    {
        return rtrim(strtr(base64_encode(json_encode(['id' => (int) $lastId])), '+/', '-_'), '=');
    }

    /**
     * @return int the last id seen (0 when no cursor was given)
     * @throws ValidationException when the cursor is malformed
     */
    public static function decode($cursor)
    {
        if ($cursor === null || $cursor === '') {
            return 0;
        }

        $json = base64_decode(strtr($cursor, '-_', '+/'), true);
        $data = $json === false ? null : json_decode($json, true);

        if (! is_array($data) || ! isset($data['id']) || ! is_int($data['id']) || $data['id'] < 0) {
            throw ValidationException::withMessages(['cursor' => 'The cursor is invalid. Use the next_cursor value from the previous page.']);
        }

        return $data['id'];
    }

    /**
     * Fetch one page of rows ordered by $idColumn using limit + 1 rows to
     * detect whether another page exists.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return array [array $rows, string|null $nextCursor]
     */
    public static function paginate($query, $idColumn, $idAttribute, $cursor, $limit)
    {
        $rows = $query
            ->where($idColumn, '>', self::decode($cursor))
            ->orderBy($idColumn)
            ->limit($limit + 1)
            ->get()
            ->all();

        $next = null;
        if (count($rows) > $limit) {
            array_pop($rows);
            $next = self::encode(end($rows)->{$idAttribute});
        }

        return [$rows, $next];
    }
}
