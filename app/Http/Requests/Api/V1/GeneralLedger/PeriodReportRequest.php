<?php

namespace App\Http\Requests\Api\V1\GeneralLedger;

use App\Http\Requests\Api\V1\ApiRequest;
use Carbon\Carbon;

/**
 * Base for report endpoints: a required, bounded date range.
 */
abstract class PeriodReportRequest extends ApiRequest
{
    protected function periodRules()
    {
        return [
            'date_from' => ['required', 'date_format:Y-m-d'],
            'date_to' => ['required', 'date_format:Y-m-d', 'after_or_equal:date_from'],
        ];
    }

    public function withValidator($validator)
    {
        parent::withValidator($validator);

        $validator->after(function ($validator) {
            if ($validator->errors()->has('date_from') || $validator->errors()->has('date_to')) {
                return;
            }
            $max = (int) config('api.general_ledger.max_report_days');
            $days = Carbon::parse($this->query('date_from'))->diffInDays(Carbon::parse($this->query('date_to')));
            if ($days > $max) {
                $validator->errors()->add('date_to', 'The date range may not exceed ' . $max . ' days.');
            }
        });
    }

    public function dateFrom()
    {
        return $this->query('date_from');
    }

    public function dateTo()
    {
        return $this->query('date_to');
    }
}
