<?php

namespace App\Services\GeneralLedger;

use App\Support\Api\Format;
use App\Support\GeneralLedger\VoucherStatus;
use App\Support\GeneralLedger\VoucherType;
use Illuminate\Support\Facades\DB;

/**
 * Query builders and row transformers for vouchers (fin_general_ledgers)
 * and their lines (fin_general_ledger_details).
 *
 * All filters use bound parameters; column names are never taken from input.
 */
class LedgerQuery
{
    /**
     * Voucher headers with line totals aggregated from the detail table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function vouchers(array $filters)
    {
        $totals = DB::table('fin_general_ledger_details')
            ->select('fin_gen_id')
            ->selectRaw('COUNT(*) AS line_count')
            ->selectRaw('COALESCE(SUM(debit), 0) AS total_debit')
            ->selectRaw('COALESCE(SUM(credit), 0) AS total_credit')
            ->groupBy('fin_gen_id');

        $query = DB::table('fin_general_ledgers as g')
            ->leftJoinSub($totals, 't', 't.fin_gen_id', '=', 'g.id')
            ->select(
                'g.id', 'g.txn_type', 'g.txn_series', 'g.txn_date', 'g.voucher_status', 'g.details',
                'g.vendor_id', 'g.cheque_id', 'g.cheque_number', 'g.office_id', 'g.user_id',
                'g.created_at', 'g.updated_at', 't.line_count', 't.total_debit', 't.total_credit'
            );

        $this->applyHeaderFilters($query, $filters);

        if (! empty($filters['updated_since'])) {
            $query->where('g.updated_at', '>=', $filters['updated_since']);
        }

        return $query;
    }

    /**
     * Journal lines joined to their voucher header. Lines whose header no
     * longer exists are excluded (same as the web reports).
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function entries(array $filters)
    {
        $query = DB::table('fin_general_ledger_details as d')
            ->join('fin_general_ledgers as g', 'g.id', '=', 'd.fin_gen_id')
            ->leftJoin('fin_chart_of_accounts as a', 'a.id', '=', 'd.coa_id')
            ->select(
                'd.id', 'd.fin_gen_id', 'd.coa_id', 'd.detail', 'd.debit', 'd.credit',
                'd.created_at', 'd.updated_at', 'a.code as account_code',
                'g.txn_type', 'g.txn_series', 'g.txn_date', 'g.voucher_status', 'g.vendor_id'
            );

        $this->applyHeaderFilters($query, $filters);

        if (! empty($filters['account_id'])) {
            $query->where('d.coa_id', $filters['account_id']);
        }
        if (! empty($filters['account_code'])) {
            $query->where('a.code', 'like', self::escapeLike($filters['account_code']) . '%');
        }
        if (! empty($filters['voucher_id'])) {
            $query->where('d.fin_gen_id', $filters['voucher_id']);
        }
        if (! empty($filters['updated_since'])) {
            $since = $filters['updated_since'];
            $query->where(function ($q) use ($since) {
                $q->where('g.updated_at', '>=', $since)->orWhere('d.updated_at', '>=', $since);
            });
        }

        return $query;
    }

    /**
     * Debit/credit sums per account: before date_from (opening) and within
     * [date_from, date_to] (period). Only posted vouchers are included.
     *
     * @return array rows {coa_id, opening_debit, opening_credit, period_debit, period_credit}
     */
    public function accountTotals($dateFrom, $dateTo)
    {
        return DB::table('fin_general_ledger_details as d')
            ->join('fin_general_ledgers as g', 'g.id', '=', 'd.fin_gen_id')
            ->whereIn('g.voucher_status', VoucherStatus::posted())
            ->where('g.txn_date', '<=', $dateTo)
            ->groupBy('d.coa_id')
            ->select('d.coa_id')
            ->selectRaw('COALESCE(SUM(CASE WHEN g.txn_date < ? THEN d.debit END), 0) AS opening_debit', [$dateFrom])
            ->selectRaw('COALESCE(SUM(CASE WHEN g.txn_date < ? THEN d.credit END), 0) AS opening_credit', [$dateFrom])
            ->selectRaw('COALESCE(SUM(CASE WHEN g.txn_date >= ? THEN d.debit END), 0) AS period_debit', [$dateFrom])
            ->selectRaw('COALESCE(SUM(CASE WHEN g.txn_date >= ? THEN d.credit END), 0) AS period_credit', [$dateFrom])
            ->get()
            ->all();
    }

    /**
     * Debit/credit per account per calendar month within the range.
     *
     * @return array rows {period, coa_id, account_code, debit, credit, line_count}
     */
    public function monthlyTotals($dateFrom, $dateTo, $accountCode = null)
    {
        $query = DB::table('fin_general_ledger_details as d')
            ->join('fin_general_ledgers as g', 'g.id', '=', 'd.fin_gen_id')
            ->leftJoin('fin_chart_of_accounts as a', 'a.id', '=', 'd.coa_id')
            ->whereIn('g.voucher_status', VoucherStatus::posted())
            ->whereBetween('g.txn_date', [$dateFrom, $dateTo])
            ->selectRaw("DATE_FORMAT(g.txn_date, '%Y-%m') AS period")
            ->addSelect('d.coa_id', 'a.code as account_code')
            ->selectRaw('COALESCE(SUM(d.debit), 0) AS debit')
            ->selectRaw('COALESCE(SUM(d.credit), 0) AS credit')
            ->selectRaw('COUNT(*) AS line_count')
            ->groupBy('period', 'd.coa_id', 'a.code')
            ->orderBy('period')
            ->orderBy('a.code');

        if ($accountCode) {
            $query->where('a.code', 'like', self::escapeLike($accountCode) . '%');
        }

        return $query->get()->all();
    }

    protected function applyHeaderFilters($query, array $filters)
    {
        $query->whereIn('g.voucher_status', $filters['statuses']);

        if (! empty($filters['date_from'])) {
            $query->where('g.txn_date', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $query->where('g.txn_date', '<=', $filters['date_to']);
        }
        if (! empty($filters['types'])) {
            $query->whereIn('g.txn_type', $filters['types']);
        }
        if (! empty($filters['vendor_id'])) {
            $query->where('g.vendor_id', $filters['vendor_id']);
        }
    }

    public static function transformVoucher($row)
    {
        $debit = Format::amount($row->total_debit);
        $credit = Format::amount($row->total_credit);

        return [
            'voucher_id' => (int) $row->id,
            'voucher_no' => VoucherType::voucherNumber($row->txn_type, $row->txn_series),
            'voucher_type_id' => Format::int($row->txn_type),
            'voucher_type' => VoucherType::code($row->txn_type),
            'series' => Format::int($row->txn_series),
            'txn_date' => Format::date($row->txn_date),
            'status_id' => Format::int($row->voucher_status),
            'status' => VoucherStatus::name($row->voucher_status),
            'description' => $row->details,
            'vendor_id' => Format::int($row->vendor_id),
            'cheque_id' => Format::int($row->cheque_id),
            'cheque_number' => $row->cheque_number,
            'office_id' => Format::int($row->office_id),
            'prepared_by_user_id' => Format::int($row->user_id),
            'line_count' => (int) $row->line_count,
            'total_debit' => $debit,
            'total_credit' => $credit,
            'is_balanced' => abs($debit - $credit) < 0.005,
            'created_at' => Format::datetime($row->created_at),
            'updated_at' => Format::datetime($row->updated_at),
        ];
    }

    public static function transformEntry($row)
    {
        $debit = Format::amount($row->debit);
        $credit = Format::amount($row->credit);

        return [
            'entry_id' => (int) $row->id,
            'voucher_id' => (int) $row->fin_gen_id,
            'voucher_no' => VoucherType::voucherNumber($row->txn_type, $row->txn_series),
            'voucher_type' => VoucherType::code($row->txn_type),
            'txn_date' => Format::date($row->txn_date),
            'status_id' => Format::int($row->voucher_status),
            'account_id' => Format::int($row->coa_id),
            'account_code' => $row->account_code,
            'is_account_mapped' => $row->account_code !== null,
            'description' => $row->detail,
            'debit' => $debit,
            'credit' => $credit,
            'net_amount' => round($debit - $credit, 2),
            'vendor_id' => Format::int($row->vendor_id),
            'created_at' => Format::datetime($row->created_at),
            'updated_at' => Format::datetime($row->updated_at),
        ];
    }

    public static function escapeLike($value)
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
