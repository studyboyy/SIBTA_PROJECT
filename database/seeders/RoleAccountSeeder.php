<?php

namespace Database\Seeders;

use App\Models\Dosens;
use App\Models\Mahasiswas;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleAccountSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->whereIn('email', ['kaprodi@sibta.test', 'mahasiswa@sibta.test'])->get()->each(function (User $user) {
            $user->mahasiswa()?->delete();
            $user->managedProdi()?->update(['kaprodi_user_id' => null]);
            $user->delete();
        });

        $admin = User::updateOrCreate(
            ['email' => 'admin@sibta.test'],
            [
                'name' => 'Admin SIBTA',
                'password' => Hash::make('Admin123!'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['admin']);

        $kaprodiTi = User::updateOrCreate(
            ['email' => 'kaprodi.ti@sibta.test'],
            [
                'name' => 'Kaprodi Teknik Informatika',
                'password' => Hash::make('Kaprodi123!'),
                'email_verified_at' => now(),
            ]
        );
        $kaprodiTi->syncRoles(['kaprodi']);

        $kaprodiSi = User::updateOrCreate(
            ['email' => 'kaprodi.si@sibta.test'],
            [
                'name' => 'Kaprodi Sistem Informasi',
                'password' => Hash::make('Kaprodi123!'),
                'email_verified_at' => now(),
            ]
        );
        $kaprodiSi->syncRoles(['kaprodi']);

        $teknikInformatika = Prodi::updateOrCreate(
            ['name' => 'Teknik Informatika'],
            [
                'code' => 'TI',
                'kaprodi_user_id' => $kaprodiTi->id,
            ]
        );

        $sistemInformasi = Prodi::updateOrCreate(
            ['name' => 'Sistem Informasi'],
            [
                'code' => 'SI',
                'kaprodi_user_id' => $kaprodiSi->id,
            ]
        );

        $pimpinan = User::updateOrCreate(
            ['email' => 'pimpinan@sibta.test'],
            [
                'name' => 'Pimpinan SIBTA',
                'password' => Hash::make('Pimpinan123!'),
                'email_verified_at' => now(),
            ]
        );
        $pimpinan->syncRoles(['pimpinan']);

        $dosenUser = User::updateOrCreate(
            ['email' => 'dosen@sibta.test'],
            [
                'name' => 'Dosen SIBTA',
                'password' => Hash::make('Dosen123!'),
                'email_verified_at' => now(),
            ]
        );
        $dosenUser->syncRoles(['dosen']);

        Dosens::updateOrCreate(
            ['user_id' => $dosenUser->id],
            [
                'nidn' => '1987001001',
                'jabatan' => 'Lektor',
                'phone' => '081234567890',
                'kuota_bimbingan' => 10,
                'photo' => null,
            ]
        );

        $mahasiswaTiUser = User::updateOrCreate(
            ['email' => 'mahasiswa.ti@sibta.test'],
            [
                'name' => 'Mahasiswa TI SIBTA',
                'password' => Hash::make('Mahasiswa123!'),
                'email_verified_at' => now(),
            ]
        );
        $mahasiswaTiUser->syncRoles(['mahasiswa']);

        Mahasiswas::updateOrCreate(
            ['nim' => '2023001001'],
            [
                'user_id' => $mahasiswaTiUser->id,
                'nim' => '2023001001',
                'angkatan' => '2023',
                'prodi' => 'Teknik Informatika',
                'prodi_id' => $teknikInformatika->id,
                'status_ta' => 'Pending',
                'photo' => null,
            ]
        );

        $mahasiswaSiUser = User::updateOrCreate(
            ['email' => 'mahasiswa.si@sibta.test'],
            [
                'name' => 'Mahasiswa SI SIBTA',
                'password' => Hash::make('Mahasiswa123!'),
                'email_verified_at' => now(),
            ]
        );
        $mahasiswaSiUser->syncRoles(['mahasiswa']);

        Mahasiswas::updateOrCreate(
            ['nim' => '2023001002'],
            [
                'user_id' => $mahasiswaSiUser->id,
                'nim' => '2023001002',
                'angkatan' => '2023',
                'prodi' => 'Sistem Informasi',
                'prodi_id' => $sistemInformasi->id,
                'status_ta' => 'Pending',
                'photo' => null,
            ]
        );
    }
}
