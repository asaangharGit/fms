<?php

namespace App\Http\Controllers\Api\V1\GeneralLedger;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ApiRequest;
use App\Http\Requests\Api\V1\GeneralLedger\ListAccountsRequest;
use App\Services\GeneralLedger\AccountHierarchy;
use App\Support\Api\ApiResponse;
use App\Support\Api\Cursor;

/**
 * Chart of accounts dimension, flattened to L1..L5 columns.
 */
class AccountController extends Controller
{
    /** @var AccountHierarchy */
    protected $hierarchy;

    public function __construct(AccountHierarchy $hierarchy)
    {
        $this->hierarchy = $hierarchy;
    }

    /**
     * GET /api/v1/gl/accounts
     */
    public function index(ListAccountsRequest $request)
    {
        $level = $request->query('level');
        $classCode = $request->query('class_code');
        $postingOnly = $request->postingOnly();
        $afterId = Cursor::decode($request->query('cursor'));
        $limit = $request->limit();

        $rows = array_values(array_filter($this->hierarchy->all(), function ($row) use ($level, $classCode, $postingOnly, $afterId) {
            return $row['account_id'] > $afterId
                && ($level === null || $row['level'] === $level)
                && ($classCode === null || $row['class_code'] === $classCode)
                && (! $postingOnly || $row['is_posting_account']);
        }));

        usort($rows, function ($a, $b) {
            return $a['account_id'] - $b['account_id'];
        });

        $next = null;
        if (count($rows) > $limit) {
            $rows = array_slice($rows, 0, $limit);
            $next = Cursor::encode($rows[$limit - 1]['account_id']);
        }

        return ApiResponse::paginated($request, $rows, $limit, $next, ['filters' => $request->appliedFilters()]);
    }

    /**
     * GET /api/v1/gl/accounts/{account}
     */
    public function show(ApiRequest $request, $account)
    {
        $row = $this->hierarchy->find((int) $account);

        if ($row === null) {
            return ApiResponse::error($request, 404, 'not_found', 'Account not found.');
        }

        return ApiResponse::item($row);
    }
}
