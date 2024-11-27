<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->index();
            $table->string('registration_number', 50)->unique();
            $table->date('foundation_date')->nullable();
            $table->string('activity', 100)->nullable()->index();
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });

        /* Add database update restrict to company_foundation_date */
        DB::unprepared('
            CREATE TRIGGER prevent_update_on_company_foundation_date
            BEFORE UPDATE ON companies
            FOR EACH ROW
            BEGIN
                IF OLD.foundation_date IS NOT NULL AND NEW.foundation_date <> OLD.foundation_date THEN
                    SIGNAL SQLSTATE "45000"
                    SET MESSAGE_TEXT = "A foundation_date field is not modifiable.";
                END IF;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS prevent_update_on_company_foundation_date;');
        Schema::dropIfExists('companies');
    }
};
