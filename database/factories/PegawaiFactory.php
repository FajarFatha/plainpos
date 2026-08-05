<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PegawaiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nip' => fake()->unique()->numerify('##########'),
            'nama_pegawai' => fake()->name(),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'is_active' => true,
        ];
    }
}