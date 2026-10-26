<?php

namespace App\Support\Api;

use Carbon\Carbon;

/**
 * Output formatting shared by API transformers.
 */
class Format
{
    /** Money as a JSON number rounded to 2 decimals (DB columns are DECIMAL(10,2)). */
    public static function amount($value)
    {
        return round((float) $value, 2);
    }

    /** DATE column as "YYYY-MM-DD". */
    public static function date($value)
    {
        return $value === null ? null : substr((string) $value, 0, 10);
    }

    /** DATETIME column (stored in app timezone) as ISO 8601 with offset. */
    public static function datetime($value)
    {
        if ($value === null || $value === '' || strpos((string) $value, '0000-00-00') === 0) {
            return null;
        }

        return Carbon::parse($value, config('app.timezone'))->toIso8601String();
    }

    /** Nullable integer. */
    public static function int($value)
    {
        return $value === null ? null : (int) $value;
    }
}
