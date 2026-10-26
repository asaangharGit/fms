<?php

namespace App\Http\Controllers\Api\V1\GeneralLedger;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ApiRequest;
use App\Support\Api\ApiResponse;
use App\Support\Api\Format;
use App\Support\GeneralLedger\VoucherStatus;
use Illuminate\Support\Facades\DB;

/**
 * GET /api/v1/gl — connectivity check, data coverage and data-quality
 * indicators. Use it as the first query in Power BI to validate the key.
 */
class MetaController extends Controller
{
    public function show(ApiRequest $request)
    {
        $client = $request->attributes->get('api_client');
        $posted = VoucherStatus::posted();

        $coverage = DB::table('fin_general_ledgers')
            ->whereIn('voucher_status', $posted)
            ->selectRaw('MIN(txn_date) AS first_txn_date, MAX(txn_date) AS last_txn_date, COUNT(*) AS vouchers, MAX(updated_at) AS last_updated_at')
            ->first();

        $entries = DB::table('fin_general_ledger_details as d')
            ->join('fin_general_ledgers as g', 'g.id', '=', 'd.fin_gen_id')
            ->whereIn('g.voucher_status', $posted)
            ->count();

        return ApiResponse::item([
            'api' => 'FMS General Ledger API',
            'version' => 'v1',
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'scopes' => $client->scopes,
                'expires_at' => $client->expires_at ? $client->expires_at->toIso8601String() : null,
            ],
            'posted_status_ids' => $posted,
            'timezone' => config('app.timezone'),
            'coverage' => [
                'first_txn_date' => Format::date($coverage->first_txn_date),
                'last_txn_date' => Format::date($coverage->last_txn_date),
                'posted_vouchers' => (int) $coverage->vouchers,
                'posted_entries' => $entries,
                'last_updated_at' => Format::datetime($coverage->last_updated_at),
            ],
            'data_quality' => $this->dataQuality($posted),
            'endpoints' => [
                'accounts' => route('api.v1.gl.accounts.index'),
                'voucher_types' => route('api.v1.gl.voucher-types'),
                'voucher_statuses' => route('api.v1.gl.voucher-statuses'),
                'account_classes' => route('api.v1.gl.account-classes'),
                'vouchers' => route('api.v1.gl.vouchers.index'),
                'entries' => route('api.v1.gl.entries.index'),
                'trial_balance' => route('api.v1.gl.reports.trial-balance'),
                'monthly_balances' => route('api.v1.gl.reports.monthly-balances'),
            ],
        ]);
    }

    /**
     * Counts of known data issues in posted vouchers, so BI dashboards can
     * surface them instead of silently showing wrong totals.
     */
    protected function dataQuality(array $posted)
    {
        $statusList = implode(',', array_map('intval', $posted));

        $unbalanced = DB::selectOne("
            SELECT COUNT(*) AS c FROM (
                SELECT g.id
                FROM fin_general_ledgers g
                JOIN fin_general_ledger_details d ON d.fin_gen_id = g.id
                WHERE g.voucher_status IN ($statusList)
                GROUP BY g.id
                HAVING ABS(COALESCE(SUM(d.debit), 0) - COALESCE(SUM(d.credit), 0)) >= 0.005
            ) x")->c;

        $unmapped = DB::table('fin_general_ledger_details as d')
            ->join('fin_general_ledgers as g', 'g.id', '=', 'd.fin_gen_id')
            ->leftJoin('fin_chart_of_accounts as a', 'a.id', '=', 'd.coa_id')
            ->whereIn('g.voucher_status', $posted)
            ->whereNull('a.id')
            ->count();

        $orphanLines = DB::table('fin_general_ledger_details as d')
            ->leftJoin('fin_general_ledgers as g', 'g.id', '=', 'd.fin_gen_id')
            ->whereNull('g.id')
            ->count();

        return [
            'unbalanced_vouchers' => (int) $unbalanced,
            'entries_with_unknown_account' => $unmapped,
            'lines_without_voucher_excluded' => $orphanLines,
        ];
    }
}
