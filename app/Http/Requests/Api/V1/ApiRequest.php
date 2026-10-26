<?php

namespace App\Http\Requests\Api\V1;

use App\Support\GeneralLedger\VoucherStatus;
use App\Support\GeneralLedger\VoucherType;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Base request for API v1 endpoints.
 *
 * - Authorization is handled by the api.client middleware (scopes), so
 *   authorize() always returns true here.
 * - Query parameters not listed in parameters() are rejected with 422 so
 *   typos in Power BI queries fail loudly instead of silently returning
 *   unfiltered data.
 */
class ApiRequest extends FormRequest
{
    const STATUS_PATTERN = '/^(posted|all|[1-4](,[1-4])*)$/i';
    const TYPE_PATTERN = '/^([1-5]|bpv|brv|cpv|crv|jv)(,([1-5]|bpv|brv|cpv|crv|jv))*$/i';
    const ACCOUNT_CODE_PATTERN = '/^[0-9]{2}(-[0-9]{1,4}){0,4}-?$/';

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }

    /**
     * Query parameters accepted by the endpoint (keys of rules() by default).
     */
    protected function parameters()
    {
        return array_keys($this->rules());
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $allowed = $this->parameters();
            $allowed[] = config('api.auth.query_key_name', 'api_key');

            foreach (array_keys($this->query()) as $key) {
                if (! in_array($key, $allowed, true)) {
                    $validator->errors()->add($key, 'Unknown query parameter "' . $key . '".');
                }
            }
        });
    }

    public function validationData()
    {
        return $this->query();
    }

    /**
     * An unencoded "+" in "2026-10-01T00:00:00+05:00" arrives as a space;
     * restore it so the timestamp keeps its offset.
     */
    protected function prepareForValidation()
    {
        $since = $this->query('updated_since');

        if (is_string($since) && preg_match('/^(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(?::\d{2})?(?:\.\d+)?) (\d{2}:?\d{2})$/', $since, $m)) {
            $this->query->set('updated_since', $m[1] . '+' . $m[2]);
        }
    }

    /* ----------------------------------------------------------------
     | Shared rule sets
     * ---------------------------------------------------------------- */

    protected function paginationRules()
    {
        return [
            'cursor' => ['nullable', 'string', 'max:200'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:' . config('api.pagination.max_limit')],
        ];
    }

    protected function voucherFilterRules()
    {
        return [
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
            'status' => ['nullable', 'string', 'regex:' . self::STATUS_PATTERN],
            'type' => ['nullable', 'string', 'regex:' . self::TYPE_PATTERN],
            'vendor_id' => ['nullable', 'integer', 'min:1'],
            'updated_since' => ['nullable', 'date'],
        ];
    }

    /* ----------------------------------------------------------------
     | Normalised accessors
     * ---------------------------------------------------------------- */

    public function limit()
    {
        return (int) $this->query('limit', config('api.pagination.default_limit'));
    }

    /**
     * status=posted (default) | all | comma-separated status ids.
     *
     * @return int[]
     */
    public function statuses()
    {
        $status = strtolower((string) $this->query('status', 'posted'));

        if ($status === 'posted' || $status === '') {
            return VoucherStatus::posted();
        }
        if ($status === 'all') {
            return array_keys(VoucherStatus::ALL);
        }

        return array_values(array_unique(array_map('intval', explode(',', $status))));
    }

    /**
     * @return int[] voucher type ids (empty = all types)
     */
    public function types()
    {
        $type = $this->query('type');
        if ($type === null || $type === '') {
            return [];
        }

        return array_values(array_unique(array_map([VoucherType::class, 'resolve'], explode(',', $type))));
    }

    /**
     * updated_since converted to the DB timezone (columns are stored in app timezone).
     */
    public function updatedSince()
    {
        $value = $this->query('updated_since');

        return $value ? Carbon::parse($value)->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s') : null;
    }

    public function voucherFilters()
    {
        return [
            'statuses' => $this->statuses(),
            'types' => $this->types(),
            'date_from' => $this->query('date_from'),
            'date_to' => $this->query('date_to'),
            'vendor_id' => $this->query('vendor_id'),
            'updated_since' => $this->updatedSince(),
        ];
    }

    /**
     * Echo of the applied filters for response meta (helps debugging in Power BI).
     */
    public function appliedFilters()
    {
        $applied = [];
        foreach ($this->parameters() as $key) {
            if (! in_array($key, ['cursor', 'limit'], true) && $this->query($key) !== null) {
                $applied[$key] = $this->query($key);
            }
        }
        if (in_array('status', $this->parameters(), true)) {
            $applied['status_ids'] = $this->statuses();
        }

        return $applied;
    }
}
