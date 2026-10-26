<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * roles was the only MyISAM table: it ignored transactions, so a failed
 * request could leave roles half-changed. InnoDB matches every other table.
 * Data is preserved; the migration is skipped when already InnoDB.
 */
class ConvertRolesTableToInnodb extends Migration
{
    public function up()
    {
        if ($this->engine() !== 'InnoDB') {
            DB::statement('ALTER TABLE `roles` ENGINE = InnoDB');
        }
    }

    public function down()
    {
        // Intentionally not converting back to MyISAM.
    }

    protected function engine()
    {
        $row = DB::selectOne(
            'SELECT ENGINE AS engine FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?',
            ['roles']
        );

        return $row ? $row->engine : null;
    }
}
