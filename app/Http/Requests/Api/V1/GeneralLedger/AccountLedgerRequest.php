<?php

namespace App\Http\Requests\Api\V1\GeneralLedger;

class AccountLedgerRequest extends PeriodReportRequest
{
    public function rules()
    {
        return $this->periodRules();
    }
}
