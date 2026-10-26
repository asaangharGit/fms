<?php

namespace App\Support\GeneralLedger;

/**
 * fin_general_ledgers.txn_type values (see FinGeneralLedgerController::getReference).
 */
class VoucherType
{
    const BANK_PAYMENT = 1;
    const BANK_RECEIPT = 2;
    const CASH_PAYMENT = 3;
    const CASH_RECEIPT = 4;
    const JOURNAL = 5;

    const ALL = [
        self::BANK_PAYMENT => ['code' => 'BPV', 'name' => 'Bank Payment Voucher'],
        self::BANK_RECEIPT => ['code' => 'BRV', 'name' => 'Bank Receipt Voucher'],
        self::CASH_PAYMENT => ['code' => 'CPV', 'name' => 'Cash Payment Voucher'],
        self::CASH_RECEIPT => ['code' => 'CRV', 'name' => 'Cash Receipt Voucher'],
        self::JOURNAL => ['code' => 'JV', 'name' => 'Journal Voucher'],
    ];

    public static function code($id)
    {
        return isset(self::ALL[$id]) ? self::ALL[$id]['code'] : null;
    }

    public static function name($id)
    {
        return isset(self::ALL[$id]) ? self::ALL[$id]['name'] : null;
    }

    /**
     * Voucher number as printed on vouchers, e.g. BPV001 / JV1234.
     */
    public static function voucherNumber($typeId, $series)
    {
        $code = self::code($typeId);

        return $code === null || $series === null ? null : $code . str_pad((string) $series, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Resolve "1", "jv" or "BPV" to a type id, or null when unknown.
     */
    public static function resolve($value)
    {
        $value = strtoupper(trim((string) $value));
        if (ctype_digit($value)) {
            return isset(self::ALL[(int) $value]) ? (int) $value : null;
        }
        foreach (self::ALL as $id => $type) {
            if ($type['code'] === $value) {
                return $id;
            }
        }

        return null;
    }

    public static function toArray()
    {
        $rows = [];
        foreach (self::ALL as $id => $type) {
            $rows[] = ['type_id' => $id, 'code' => $type['code'], 'name' => $type['name']];
        }

        return $rows;
    }
}
