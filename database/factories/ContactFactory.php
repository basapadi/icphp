<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['pelanggan','pemasok']),
            'nama' => fake()->firstNameMale().' '.fake()->lastName(),
            'alamat' => fake()->address(),
            'telepon' => fake()->e164PhoneNumber(),
            'email' => fake()->randomElement(['bachtiarpanjaitan0@gmail.com','dimasspanjaitan@gmail.com']),
            'status' => 1
        ];
    }
}
