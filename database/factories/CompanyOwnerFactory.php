<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanyOwner;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyOwnerFactory extends Factory
{
    protected $model = CompanyOwner::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'active' => $this->faker->boolean,
            'order' => $this->faker->randomDigitNotZero(),
        ];
    }
}
