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
        DB::statement('DROP PROCEDURE IF EXISTS GenerateDateRangeWithCompanyNames');

        DB::unprepared("
            CREATE PROCEDURE GenerateDateRangeWithCompanyNames(IN start_date DATE, IN end_date DATE, IN page INT, IN perPage INT)
            BEGIN

                DROP TEMPORARY TABLE IF EXISTS date_range;

                CREATE TEMPORARY TABLE date_range (
                    date DATE
                );

                WHILE start_date <= end_date DO
                    INSERT INTO date_range (date) VALUES (start_date);
                    SET start_date = DATE_ADD(start_date, INTERVAL 1 DAY);
                END WHILE;

                SET @offset = (page - 1) * perPage;

                SET @sql = CONCAT(
                    'SELECT d.date AS company_foundation_date, c.name
                    FROM date_range d
                    LEFT JOIN companies c ON c.foundation_date = d.date
                    ORDER BY d.date ASC
                    LIMIT ', perPage, ' OFFSET ', @offset
                );

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
        DB::statement('DROP PROCEDURE IF EXISTS GenerateDateRangeWithCompanyNames');
    }
};
