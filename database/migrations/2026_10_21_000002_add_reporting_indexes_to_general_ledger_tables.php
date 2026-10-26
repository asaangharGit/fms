<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Non-unique indexes used by the reports and the General Ledger API.
 * Indexes that already exist are skipped, so the migration is safe to run
 * on databases where they were added by hand.
 */
class AddReportingIndexesToGeneralLedgerTables extends Migration
{
    protected $indexes = [
        'fin_general_ledgers' => [
            'fin_gl_txn_date_status_index' => ['txn_date', 'voucher_status'],
            'fin_gl_updated_at_index' => ['updated_at'],
        ],
        'fin_general_ledger_details' => [
            'fin_gld_fin_gen_id_index' => ['fin_gen_id'],
            'fin_gld_coa_id_index' => ['coa_id'],
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->indexes as $table => $indexes) {
            foreach ($indexes as $name => $columns) {
                if (! $this->indexExists($table, $name)) {
                    Schema::table($table, function (Blueprint $blueprint) use ($columns, $name) {
                        $blueprint->index($columns, $name);
                    });
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->indexes as $table => $indexes) {
            foreach (array_keys($indexes) as $name) {
                if ($this->indexExists($table, $name)) {
                    Schema::table($table, function (Blueprint $blueprint) use ($name) {
                        $blueprint->dropIndex($name);
                    });
                }
            }
        }
    }

    protected function indexExists($table, $name)
    {
        return count(DB::select('SHOW INDEX FROM `' . $table . '` WHERE Key_name = ?', [$name])) > 0;
    }
}
