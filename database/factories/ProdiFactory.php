<?php

namespace Database\Factories;

use App\Models\Prodi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prodi>
 */
class ProdiFactory extends Factory
{
    protected $model = Prodi::class;

    public function definition(): array
    {
        $prodis = [
            ['name' => 'Teknik Informatika', 'code' => 'TI'],
            ['name' => 'Sistem Informasi', 'code' => 'SI'],
        ];

        return fake()->randomElement($prodis);
    }

    public function teknikInformatika(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Teknik Informatika',
            'code' => 'TI',
        ]);
    }

    public function sistemInformasi(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Sistem Informasi',
            'code' => 'SI',
        ]);
    }
}
