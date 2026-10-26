<?php

namespace App\Support\GeneralLedger;

/**
 * Top-level (L1) account classes of the chart of accounts and their
 * normal balance side.
 */
class AccountClass
{
    const ALL = [
        '01' => ['name' => 'Assets', 'normal_balance' => 'debit', 'statement' => 'balance_sheet'],
        '02' => ['name' => 'Liabilities', 'normal_balance' => 'credit', 'statement' => 'balance_sheet'],
        '03' => ['name' => 'Equity', 'normal_balance' => 'credit', 'statement' => 'balance_sheet'],
        '04' => ['name' => 'Expenses', 'normal_balance' => 'debit', 'statement' => 'profit_and_loss'],
        '05' => ['name' => 'Income', 'normal_balance' => 'credit', 'statement' => 'profit_and_loss'],
    ];

    /**
     * Class code (first segment) of any account code, e.g. "04-02-001" => "04".
     */
    public static function codeOf($accountCode)
    {
        $code = substr((string) $accountCode, 0, 2);

        return isset(self::ALL[$code]) ? $code : null;
    }

    public static function get($classCode)
    {
        return isset(self::ALL[$classCode]) ? self::ALL[$classCode] : null;
    }
}
