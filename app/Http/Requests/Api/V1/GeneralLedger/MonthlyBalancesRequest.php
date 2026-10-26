<?php

namespace App\Http\Requests\Api\V1\GeneralLedger;

class MonthlyBalancesRequest extends PeriodReportRequest
{
    public function rules()
    {
        return array_merge($this->periodRules(), [
            'account_code' => ['nullable', 'string', 'max:30', 'regex:' . self::ACCOUNT_CODE_PATTERN],
        ]);
    }
}
