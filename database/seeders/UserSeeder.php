<?php

namespace Database\Seeders;




use App\Models\Mahasiswas;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->has(Mahasiswas::factory())
            ->count(150)
            ->create()->each(function ($user) {
                $user->assignRole('mahasiswa');
            });
    }
}
