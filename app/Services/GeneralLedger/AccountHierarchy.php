<?php

namespace App\Services\GeneralLedger;

use App\Support\Api\Format;
use App\Support\GeneralLedger\AccountClass;
use Illuminate\Support\Facades\DB;

/**
 * Flattens the 5-level chart of accounts (fin_chart_of_accounts, linked by
 * parent_code) into one row per account carrying all its ancestors, which
 * is the shape Power BI needs for a hierarchy / drill-down dimension.
 */
class AccountHierarchy
{
    const LEVELS = 5;

    /** @var array|null accounts keyed by id */
    protected $byId;

    /** @var array|null accounts keyed by code */
    protected $byCode;

    /** @var array flattened rows keyed by id */
    protected $flat = [];

    /**
     * Flattened row for every account, ordered by code.
     */
    public function all()
    {
        $this->load();

        $rows = [];
        foreach ($this->byCode as $account) {
            $rows[] = $this->row($account->id);
        }

        return $rows;
    }

    /**
     * Flattened row for one account, or null when the id is unknown.
     */
    public function find($id)
    {
        $this->load();

        return isset($this->byId[$id]) ? $this->row($id) : null;
    }

    /**
     * Ancestor (or self) of an account at a given level (1-5), as
     * ['account_id', 'code', 'title'], or null when it cannot be resolved.
     */
    public function ancestorAt($id, $levelNumber)
    {
        $row = $this->find($id);
        if ($row === null || $row['level_number'] === null || $row['level_number'] < $levelNumber) {
            return null;
        }

        $code = $row['l' . $levelNumber . '_code'];
        if ($code === null || ! isset($this->byCode[$code])) {
            return null;
        }

        $account = $this->byCode[$code];

        return ['account_id' => (int) $account->id, 'code' => $account->code, 'title' => $account->title];
    }

    protected function load()
    {
        if ($this->byId !== null) {
            return;
        }

        $accounts = DB::table('fin_chart_of_accounts')
            ->select('id', 'code', 'parent_code', 'title', 'level', 'created_at', 'updated_at')
            ->orderBy('code')
            ->get();

        $this->byId = [];
        $this->byCode = [];
        foreach ($accounts as $account) {
            $this->byId[$account->id] = $account;
            $this->byCode[$account->code] = $account;
        }
    }

    protected function row($id)
    {
        if (isset($this->flat[$id])) {
            return $this->flat[$id];
        }

        $account = $this->byId[$id];
        $levelNumber = self::levelNumber($account->level);

        $levels = array_fill(1, self::LEVELS, null);
        $complete = true;
        $node = $account;
        $guard = 0;
        while ($node !== null && $guard++ < self::LEVELS) {
            $n = self::levelNumber($node->level);
            if ($n !== null) {
                $levels[$n] = $node;
            }
            if ($n === 1 || $n === null) {
                break;
            }
            $parent = isset($this->byCode[$node->parent_code]) ? $this->byCode[$node->parent_code] : null;
            if ($parent === null) {
                $complete = false;
            }
            $node = $parent;
        }

        $classCode = AccountClass::codeOf($account->code);
        $class = AccountClass::get($classCode);

        $row = [
            'account_id' => (int) $account->id,
            'code' => $account->code,
            'title' => $account->title,
            'level' => $account->level,
            'level_number' => $levelNumber,
            'parent_code' => $account->parent_code === '0' ? null : $account->parent_code,
            'is_posting_account' => $levelNumber === self::LEVELS,
            'class_code' => $classCode,
            'class_name' => $class ? $class['name'] : null,
            'normal_balance' => $class ? $class['normal_balance'] : null,
            'financial_statement' => $class ? $class['statement'] : null,
        ];
        for ($i = 1; $i <= self::LEVELS; $i++) {
            $row['l' . $i . '_code'] = $levels[$i] ? $levels[$i]->code : null;
            $row['l' . $i . '_title'] = $levels[$i] ? $levels[$i]->title : null;
        }
        $row['hierarchy_complete'] = $complete && $levelNumber !== null;
        $row['created_at'] = Format::datetime($account->created_at);
        $row['updated_at'] = Format::datetime($account->updated_at);

        return $this->flat[$id] = $row;
    }

    public static function levelNumber($level)
    {
        return preg_match('/^L([1-5])$/', (string) $level, $m) ? (int) $m[1] : null;
    }
}
