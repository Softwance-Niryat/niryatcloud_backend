<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DatabaseCloneService
{
    /**
     * Clone a MySQL database by copying structure + data
     */
    public function cloneDatabase(string $sourceDb, string $targetDb): void
    {
        // 1. Create the new database
        DB::statement("CREATE DATABASE IF NOT EXISTS `$targetDb`");

        // 2. Fetch list of tables in source DB
        $tables = DB::select("
            SELECT TABLE_NAME 
            FROM information_schema.TABLES 
            WHERE TABLE_SCHEMA = ?
        ", [$sourceDb]);

        foreach ($tables as $table) {

            $tableName = $table->TABLE_NAME;

            // 3. Copy table structure
            DB::statement("
                CREATE TABLE `$targetDb`.`$tableName` 
                LIKE `$sourceDb`.`$tableName`
            ");

            // 4. Copy table data
            DB::statement("
                INSERT INTO `$targetDb`.`$tableName`
                SELECT * FROM `$sourceDb`.`$tableName`
            ");
        }
    }
}
