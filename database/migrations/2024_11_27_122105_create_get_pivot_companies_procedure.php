<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('DROP PROCEDURE IF EXISTS GetPivotCompanies');

        DB::unprepared("
            CREATE PROCEDURE GetPivotCompanies()
            BEGIN
                SET @sql = NULL;
                SELECT GROUP_CONCAT(DISTINCT
                    CONCAT(
                        'MAX(CASE WHEN activity = ''',
                        activity,
                        ''' THEN companies.name END) AS `',
                        activity,
                        '`'
                    )) INTO @sql
                FROM companies;

                SET @sql = CONCAT('SELECT ', @sql, ' FROM companies GROUP BY name');
                PREPARE stmt FROM @sql;
                EXECUTE stmt;
                DEALLOCATE PREPARE stmt;
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP PROCEDURE IF EXISTS GetPivotCompanies');
    }
};
