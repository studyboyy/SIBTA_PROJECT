<?php

namespace Database\Seeders;

use App\Models\Prodi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KaprodiSeeder extends Seeder
{
    public function run(): void
    {
        $kaprodiTi = User::updateOrCreate(
            ['email' => 'kaprodi.ti@sibta.test'],
            [
                'name' => 'Kaprodi Teknik Informatika',
                'password' => Hash::make('Kaprodi123!'),
            ]
        );

        $kaprodiSi = User::updateOrCreate(
            ['email' => 'kaprodi.si@sibta.test'],
            [
                'name' => 'Kaprodi Sistem Informasi',
                'password' => Hash::make('Kaprodi123!'),
            ]
        );

        $kaprodiTi->syncRoles(['kaprodi']);
        $kaprodiSi->syncRoles(['kaprodi']);

        Prodi::updateOrCreate(
            ['name' => 'Teknik Informatika'],
            ['code' => 'TI', 'kaprodi_user_id' => $kaprodiTi->id]
        );

        Prodi::updateOrCreate(
            ['name' => 'Sistem Informasi'],
            ['code' => 'SI', 'kaprodi_user_id' => $kaprodiSi->id]
        );
    }
}
