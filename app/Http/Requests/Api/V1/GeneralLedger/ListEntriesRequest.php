<?php

namespace App\Http\Requests\Api\V1\GeneralLedger;

use App\Http\Requests\Api\V1\ApiRequest;

class ListEntriesRequest extends ApiRequest
{
    public function rules()
    {
        return array_merge($this->voucherFilterRules(), [
            'account_id' => ['nullable', 'integer', 'min:1'],
            'account_code' => ['nullable', 'string', 'max:30', 'regex:' . self::ACCOUNT_CODE_PATTERN],
            'voucher_id' => ['nullable', 'integer', 'min:1'],
        ], $this->paginationRules());
    }

    public function entryFilters()
    {
        return array_merge($this->voucherFilters(), [
            'account_id' => $this->query('account_id'),
            'account_code' => $this->query('account_code'),
            'voucher_id' => $this->query('voucher_id'),
        ]);
    }
}
