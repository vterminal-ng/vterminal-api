<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MerchantDetail>
 */
class MerchantDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'business_name' => $this->faker->company(),
            'business_state' => $this->faker->city(),
            'business_address' => $this->faker->streetAddress(),
            // 'business_verified_at' => null,
            // 'has_physical_location' => $this->faker->randomElement(['true', 'false'])
        ];
    }
}
