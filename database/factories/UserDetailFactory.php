<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserDetail>
 */
class UserDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'gender' => $gender = $this->faker->randomElement(['male', 'female']),
            'first_name' => $this->faker->firstName($gender),
            'last_name' => $this->faker->lastName(),
            'date_of_birth' => $this->faker->date(),
            // 'other_names' => null,
            // 'referral_code' => null,
            // 'referrer' => null
        ];
    }
}
