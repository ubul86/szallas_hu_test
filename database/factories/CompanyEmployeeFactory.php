<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanyEmployee;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyEmployeeFactory extends Factory
{
    protected $model = CompanyEmployee::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'active' => $this->faker->boolean,
            'company_id' => Company::factory(),
        ];
    }
}
