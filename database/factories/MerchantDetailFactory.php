<?php

namespace Database\Factories;

use App\Traits\Generators;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MerchantDetail>
 */
class MerchantDetailFactory extends Factory
{
    use Generators;
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
            'merchant_code' => $this->generateMerchantCode(),
            // 'business_verified_at' => null,
            // 'has_physical_location' => $this->faker->randomElement(['true', 'false'])
        ];
    }
}
