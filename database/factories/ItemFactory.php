<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_barang' => strtoupper($this->faker->bothify('PRD-####')), 
            'nama'        => $this->faker->words(2, true), 
            'gambar'      => $this->faker->imageUrl(640, 480, 'products', true, 'Barang'), 
            'kategori'    => $this->faker->randomElement(['Elektronik', 'Pakaian', 'Makanan', 'Alat Rumah Tangga']), 
            'status'      => $this->faker->randomElement([1, 0]),
        ];
    }
}
