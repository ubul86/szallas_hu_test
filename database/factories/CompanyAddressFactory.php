<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanyAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyAddressFactory extends Factory
{
    protected $model = CompanyAddress::class;

    public function definition(): array
    {
        return [
            'country' => $this->faker->country,
            'zip_code' => $this->faker->postcode,
            'city' => $this->faker->city,
            'street_address' => $this->faker->streetAddress,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
        ];
    }
}
