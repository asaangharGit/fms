<?php

namespace App\Http\Controllers\Api\V1\GeneralLedger;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ApiRequest;
use App\Http\Requests\Api\V1\GeneralLedger\ListVouchersRequest;
use App\Services\GeneralLedger\LedgerQuery;
use App\Support\Api\ApiResponse;
use App\Support\Api\Cursor;
use App\Support\GeneralLedger\VoucherStatus;

/**
 * Voucher headers (fin_general_ledgers) — one row per voucher.
 */
class VoucherController extends Controller
{
    /** @var LedgerQuery */
    protected $ledger;

    public function __construct(LedgerQuery $ledger)
    {
        $this->ledger = $ledger;
    }

    /**
     * GET /api/v1/gl/vouchers
     */
    public function index(ListVouchersRequest $request)
    {
        list($rows, $next) = Cursor::paginate(
            $this->ledger->vouchers($request->voucherFilters()),
            'g.id', 'id', $request->query('cursor'), $request->limit()
        );

        return ApiResponse::paginated(
            $request,
            array_map([LedgerQuery::class, 'transformVoucher'], $rows),
            $request->limit(),
            $next,
            ['filters' => $request->appliedFilters()]
        );
    }

    /**
     * GET /api/v1/gl/vouchers/{voucher} — header plus its lines (any status).
     */
    public function show(ApiRequest $request, $voucher)
    {
        $allStatuses = array_keys(VoucherStatus::ALL);

        $row = $this->ledger->vouchers(['statuses' => $allStatuses])->where('g.id', (int) $voucher)->first();

        if ($row === null) {
            return ApiResponse::error($request, 404, 'not_found', 'Voucher not found.');
        }

        $lines = $this->ledger->entries(['statuses' => $allStatuses, 'voucher_id' => (int) $voucher])
            ->orderBy('d.id')
            ->get()
            ->all();

        $data = LedgerQuery::transformVoucher($row);
        $data['status'] = VoucherStatus::name($row->voucher_status);
        $data['is_posted'] = in_array((int) $row->voucher_status, VoucherStatus::posted(), true);
        $data['lines'] = array_map([LedgerQuery::class, 'transformEntry'], $lines);

        return ApiResponse::item($data);
    }
}
