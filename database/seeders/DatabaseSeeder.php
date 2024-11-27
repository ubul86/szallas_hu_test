<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\CompanyAddress;
use App\Models\CompanyOwner;
use App\Models\CompanyEmployee;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Company::factory(5)->create()->each(function ($company) {
            CompanyAddress::factory()->create(['company_id' => $company->id]);
            CompanyOwner::factory()->create(['company_id' => $company->id]);
            CompanyEmployee::factory(5)->create(['company_id' => $company->id]);
        });
    }
}
