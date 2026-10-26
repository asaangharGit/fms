<?php

namespace App\Http\Controllers\Api\V1\GeneralLedger;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ApiRequest;
use App\Support\Api\ApiResponse;
use App\Support\GeneralLedger\AccountClass;
use App\Support\GeneralLedger\VoucherStatus;
use App\Support\GeneralLedger\VoucherType;

/**
 * Small static dimensions (lookup tables) for Power BI relationships.
 */
class ReferenceDataController extends Controller
{
    /**
     * GET /api/v1/gl/voucher-types
     */
    public function voucherTypes(ApiRequest $request)
    {
        return ApiResponse::collection(VoucherType::toArray());
    }

    /**
     * GET /api/v1/gl/voucher-statuses
     */
    public function voucherStatuses(ApiRequest $request)
    {
        return ApiResponse::collection(VoucherStatus::toArray());
    }

    /**
     * GET /api/v1/gl/account-classes
     */
    public function accountClasses(ApiRequest $request)
    {
        $rows = [];
        foreach (AccountClass::ALL as $code => $class) {
            $rows[] = [
                'class_code' => $code,
                'name' => $class['name'],
                'normal_balance' => $class['normal_balance'],
                'financial_statement' => $class['statement'],
            ];
        }

        return ApiResponse::collection($rows);
    }
}
