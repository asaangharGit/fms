<?php

namespace App\Http\Controllers\Api\V1\GeneralLedger;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\GeneralLedger\ListEntriesRequest;
use App\Services\GeneralLedger\LedgerQuery;
use App\Support\Api\ApiResponse;
use App\Support\Api\Cursor;

/**
 * Journal lines (fin_general_ledger_details) — the General Ledger fact table.
 */
class EntryController extends Controller
{
    /** @var LedgerQuery */
    protected $ledger;

    public function __construct(LedgerQuery $ledger)
    {
        $this->ledger = $ledger;
    }

    /**
     * GET /api/v1/gl/entries
     */
    public function index(ListEntriesRequest $request)
    {
        list($rows, $next) = Cursor::paginate(
            $this->ledger->entries($request->entryFilters()),
            'd.id', 'id', $request->query('cursor'), $request->limit()
        );

        return ApiResponse::paginated(
            $request,
            array_map([LedgerQuery::class, 'transformEntry'], $rows),
            $request->limit(),
            $next,
            ['filters' => $request->appliedFilters()]
        );
    }
}
