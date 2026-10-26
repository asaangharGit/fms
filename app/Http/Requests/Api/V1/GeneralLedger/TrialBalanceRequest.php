<?php

namespace App\Http\Requests\Api\V1\GeneralLedger;

use App\Services\GeneralLedger\AccountHierarchy;

class TrialBalanceRequest extends PeriodReportRequest
{
    public function rules()
    {
        return array_merge($this->periodRules(), [
            'level' => ['nullable', 'in:L1,L2,L3,L4,L5'],
            'include_zero' => ['nullable', 'in:true,false,1,0'],
        ]);
    }

    public function levelNumber()
    {
        return AccountHierarchy::levelNumber($this->query('level', 'L5'));
    }

    public function includeZero()
    {
        return in_array($this->query('include_zero'), ['true', '1'], true);
    }
}
