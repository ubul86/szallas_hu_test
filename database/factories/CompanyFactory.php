<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'registration_number' => $this->faker->unique()->randomNumber(8),
            'foundation_date' => $this->faker->date(),
            'activity' => $this->faker->sentence(3),
            'active' => $this->faker->boolean,
        ];
    }
}
