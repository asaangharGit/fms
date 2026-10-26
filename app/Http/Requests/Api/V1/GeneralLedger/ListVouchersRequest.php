<?php

namespace App\Http\Requests\Api\V1\GeneralLedger;

use App\Http\Requests\Api\V1\ApiRequest;

class ListVouchersRequest extends ApiRequest
{
    public function rules()
    {
        return array_merge($this->voucherFilterRules(), $this->paginationRules());
    }
}
