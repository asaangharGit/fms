<?php

namespace App\Support\GeneralLedger;

/**
 * fin_general_ledgers.voucher_status workflow values.
 */
class VoucherStatus
{
    const PREPARED = 1;
    const CHECKED = 2;
    const APPROVED = 3;
    const RECEIVED = 4;

    const ALL = [
        self::PREPARED => 'Prepared',
        self::CHECKED => 'Checked',
        self::APPROVED => 'Approved',
        self::RECEIVED => 'Received',
    ];

    public static function name($id)
    {
        return isset(self::ALL[$id]) ? self::ALL[$id] : null;
    }

    /**
     * Statuses counted as posted to the ledger (config api.general_ledger.posted_statuses).
     */
    public static function posted()
    {
        return config('api.general_ledger.posted_statuses', [self::APPROVED]);
    }

    public static function toArray()
    {
        $posted = self::posted();
        $rows = [];
        foreach (self::ALL as $id => $name) {
            $rows[] = ['status_id' => $id, 'name' => $name, 'is_posted' => in_array($id, $posted, true)];
        }

        return $rows;
    }
}
