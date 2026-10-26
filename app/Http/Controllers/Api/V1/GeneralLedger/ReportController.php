<?php

namespace App\Http\Controllers\Api\V1\GeneralLedger;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\GeneralLedger\AccountLedgerRequest;
use App\Http\Requests\Api\V1\GeneralLedger\MonthlyBalancesRequest;
use App\Http\Requests\Api\V1\GeneralLedger\TrialBalanceRequest;
use App\Services\GeneralLedger\AccountHierarchy;
use App\Services\GeneralLedger\LedgerQuery;
use App\Support\Api\ApiResponse;
use App\Support\Api\Format;
use App\Support\GeneralLedger\AccountClass;
use App\Support\GeneralLedger\VoucherStatus;
use Illuminate\Support\Facades\DB;

/**
 * Pre-aggregated General Ledger reports (posted vouchers only).
 *
 * Balances are signed debit-positive: balance = debit - credit.
 */
class ReportController extends Controller
{
    /** @var LedgerQuery */
    protected $ledger;

    /** @var AccountHierarchy */
    protected $hierarchy;

    public function __construct(LedgerQuery $ledger, AccountHierarchy $hierarchy)
    {
        $this->ledger = $ledger;
        $this->hierarchy = $hierarchy;
    }

    /**
     * GET /api/v1/gl/reports/trial-balance?date_from=&date_to=&level=L5
     */
    public function trialBalance(TrialBalanceRequest $request)
    {
        $level = $request->levelNumber();
        $groups = [];

        if ($request->includeZero()) {
            foreach ($this->hierarchy->all() as $account) {
                if ($account['level_number'] === $level) {
                    $groups[$account['code']] = $this->emptyBalance($account['account_id'], $account['code'], $account['title']);
                }
            }
        }

        foreach ($this->ledger->accountTotals($request->dateFrom(), $request->dateTo()) as $total) {
            $coaId = Format::int($total->coa_id);
            $target = $coaId === null ? null : $this->hierarchy->ancestorAt($coaId, $level);
            $rollupComplete = true;

            if ($target === null && $coaId !== null && ($own = $this->hierarchy->find($coaId)) !== null) {
                // Account exists but its parent chain is broken: report it
                // on its own row rather than as an unknown account.
                $target = ['account_id' => $own['account_id'], 'code' => $own['code'], 'title' => $own['title']];
                $rollupComplete = false;
            }

            $key = $target ? $target['code'] : 'zz-unmapped:' . $coaId;

            if (! isset($groups[$key])) {
                $groups[$key] = $target
                    ? $this->emptyBalance($target['account_id'], $target['code'], $target['title'])
                    : $this->emptyBalance($coaId, null, null);
                $groups[$key]['rollup_complete'] = $rollupComplete && $target !== null;
            }

            $groups[$key]['opening_balance'] += $total->opening_debit - $total->opening_credit;
            $groups[$key]['period_debit'] += $total->period_debit;
            $groups[$key]['period_credit'] += $total->period_credit;
        }

        ksort($groups, SORT_STRING);

        $rows = [];
        $totals = ['opening_balance' => 0, 'period_debit' => 0, 'period_credit' => 0, 'closing_debit' => 0, 'closing_credit' => 0];
        foreach ($groups as $group) {
            $row = $this->finishBalance($group, 'L' . $level);
            $rows[] = $row;
            foreach (array_keys($totals) as $field) {
                $totals[$field] += $row[$field];
            }
        }
        $totals = array_map(function ($v) {
            return round($v, 2);
        }, $totals);
        $totals['is_balanced'] = abs($totals['closing_debit'] - $totals['closing_credit']) < 0.005;

        return ApiResponse::collection($rows, [
            'date_from' => $request->dateFrom(),
            'date_to' => $request->dateTo(),
            'level' => 'L' . $level,
            'posted_status_ids' => VoucherStatus::posted(),
            'totals' => $totals,
        ]);
    }

    /**
     * GET /api/v1/gl/reports/monthly-balances?date_from=&date_to=&account_code=
     */
    public function monthlyBalances(MonthlyBalancesRequest $request)
    {
        $rows = [];
        foreach ($this->ledger->monthlyTotals($request->dateFrom(), $request->dateTo(), $request->query('account_code')) as $row) {
            $debit = Format::amount($row->debit);
            $credit = Format::amount($row->credit);
            $rows[] = [
                'period' => $row->period,
                'period_start' => $row->period . '-01',
                'account_id' => Format::int($row->coa_id),
                'account_code' => $row->account_code,
                'class_code' => AccountClass::codeOf($row->account_code),
                'debit' => $debit,
                'credit' => $credit,
                'net_amount' => round($debit - $credit, 2),
                'line_count' => (int) $row->line_count,
            ];
        }

        return ApiResponse::collection($rows, [
            'date_from' => $request->dateFrom(),
            'date_to' => $request->dateTo(),
            'posted_status_ids' => VoucherStatus::posted(),
        ]);
    }

    /**
     * GET /api/v1/gl/accounts/{account}/ledger?date_from=&date_to=
     *
     * Account statement with opening balance and running balance. For a
     * non-posting (L1-L4) account all descendant accounts are included.
     */
    public function accountLedger(AccountLedgerRequest $request, $account)
    {
        $info = $this->hierarchy->find((int) $account);
        if ($info === null) {
            return ApiResponse::error($request, 404, 'not_found', 'Account not found.');
        }

        // Posting accounts match by id; parent accounts by "<code>-" prefix
        // so "01-01" never matches a sibling such as "01-010".
        $filters = $info['is_posting_account']
            ? ['statuses' => VoucherStatus::posted(), 'account_id' => $info['account_id']]
            : ['statuses' => VoucherStatus::posted(), 'account_code' => $info['code'] . '-'];

        $opening = $this->ledger->entries($filters)
            ->where('g.txn_date', '<', $request->dateFrom())
            ->select(DB::raw('COALESCE(SUM(d.debit), 0) - COALESCE(SUM(d.credit), 0) AS balance'))
            ->value('balance');
        $opening = Format::amount($opening);

        $lines = $this->ledger->entries(array_merge($filters, ['date_from' => $request->dateFrom(), 'date_to' => $request->dateTo()]))
            ->orderBy('g.txn_date')
            ->orderBy('g.id')
            ->orderBy('d.id')
            ->get();

        $running = $opening;
        $debitTotal = 0;
        $creditTotal = 0;
        $rows = [];
        foreach ($lines as $line) {
            $row = LedgerQuery::transformEntry($line);
            $running = round($running + $row['net_amount'], 2);
            $debitTotal += $row['debit'];
            $creditTotal += $row['credit'];
            $row['running_balance'] = $running;
            $rows[] = $row;
        }

        return ApiResponse::item([
            'account' => $info,
            'date_from' => $request->dateFrom(),
            'date_to' => $request->dateTo(),
            'opening_balance' => $opening,
            'period_debit' => round($debitTotal, 2),
            'period_credit' => round($creditTotal, 2),
            'closing_balance' => $running,
            'lines' => $rows,
        ], ['count' => count($rows), 'posted_status_ids' => VoucherStatus::posted()]);
    }

    protected function emptyBalance($accountId, $code, $title)
    {
        return [
            'account_id' => $accountId,
            'account_code' => $code,
            'account_title' => $title,
            'opening_balance' => 0.0,
            'period_debit' => 0.0,
            'period_credit' => 0.0,
            'rollup_complete' => true,
        ];
    }

    protected function finishBalance(array $group, $level)
    {
        $opening = round($group['opening_balance'], 2);
        $debit = round($group['period_debit'], 2);
        $credit = round($group['period_credit'], 2);
        $closing = round($opening + $debit - $credit, 2);
        $classCode = AccountClass::codeOf($group['account_code']);
        $class = AccountClass::get($classCode);

        return [
            'account_id' => $group['account_id'],
            'account_code' => $group['account_code'],
            'account_title' => $group['account_title'],
            'level' => $group['account_code'] === null ? null : ($group['rollup_complete'] ? $level : $this->hierarchy->find($group['account_id'])['level']),
            'class_code' => $classCode,
            'class_name' => $class ? $class['name'] : null,
            'normal_balance' => $class ? $class['normal_balance'] : null,
            'is_account_mapped' => $group['account_code'] !== null,
            'rollup_complete' => $group['rollup_complete'],
            'opening_balance' => $opening,
            'period_debit' => $debit,
            'period_credit' => $credit,
            'period_net' => round($debit - $credit, 2),
            'closing_balance' => $closing,
            'closing_debit' => $closing > 0 ? $closing : 0.0,
            'closing_credit' => $closing < 0 ? -$closing : 0.0,
        ];
    }
}
