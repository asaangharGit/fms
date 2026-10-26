<?php

namespace App\Http\Requests\Api\V1\GeneralLedger;

use App\Http\Requests\Api\V1\ApiRequest;

class ListAccountsRequest extends ApiRequest
{
    public function rules()
    {
        return array_merge([
            'level' => ['nullable', 'in:L1,L2,L3,L4,L5'],
            'class_code' => ['nullable', 'in:01,02,03,04,05'],
            'posting_only' => ['nullable', 'in:true,false,1,0'],
        ], $this->paginationRules());
    }

    public function postingOnly()
    {
        return in_array($this->query('posting_only'), ['true', '1'], true);
    }
}
