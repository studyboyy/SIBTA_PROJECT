<?php

use App\Livewire\Pages\KaprodiBebanDosen;
use App\Livewire\Pages\KaprodiMahasiswaPerhatian;
use App\Livewire\Pages\KaprodiMonitoringMahasiswa;
use App\Models\Bimbingans;
use App\Models\Dosens;
use App\Models\Mahasiswas;
use App\Models\Prodi;
use App\Models\User;
use Livewire\Livewire;

function makeKaprodiMonitoringDosen(string $name, string $nidn, int $kuota = 2): Dosens
{
    $user = User::factory()->create([
        'name' => $name,
        'email' => strtolower(str_replace(' ', '.', $name)).'@kaprodi-pages.test',
    ]);

    return Dosens::query()->create([
        'user_id' => $user->id,
        'nidn' => $nidn,
        'phone' => $nidn,
        'jabatan' => 'Dosen',
        'kuota_bimbingan' => (string) $kuota,
    ]);
}

function makeKaprodiMonitoringMahasiswa(string $name, string $nim, Prodi $prodi): Mahasiswas
{
    $user = User::factory()->create([
        'name' => $name,
        'email' => strtolower(str_replace(' ', '.', $name)).'@kaprodi-mahasiswa.test',
    ]);

    return Mahasiswas::query()->create([
        'user_id' => $user->id,
        'nim' => $nim,
        'angkatan' => '2023',
        'prodi' => $prodi->name,
        'prodi_id' => $prodi->id,
        'status_ta' => 'Pending',
    ]);
}

test('kaprodi monitoring mahasiswa only shows students from managed prodi', function () {
    $kaprodi = User::factory()->create([
        'name' => 'Kaprodi TI Monitoring',
        'email' => 'kaprodi.monitoring@sibta.test',
    ]);
    $ti = Prodi::query()->create(['name' => 'Teknik Informatika', 'code' => 'TI', 'kaprodi_user_id' => $kaprodi->id]);
    $si = Prodi::query()->create(['name' => 'Sistem Informasi', 'code' => 'SI']);

    makeKaprodiMonitoringMahasiswa('Mahasiswa Prodi Kaprodi', '2309100001', $ti);
    makeKaprodiMonitoringMahasiswa('Mahasiswa Prodi Lain', '2309200001', $si);

    $this->actingAs($kaprodi);

    Livewire::test(KaprodiMonitoringMahasiswa::class)
        ->assertSee('Mahasiswa Prodi Kaprodi')
        ->assertDontSee('Mahasiswa Prodi Lain');
});

test('kaprodi beban dosen uses global active supervision for quota status', function () {
    $kaprodi = User::factory()->create([
        'name' => 'Kaprodi TI Beban',
        'email' => 'kaprodi.beban@sibta.test',
    ]);
    $ti = Prodi::query()->create(['name' => 'Teknik Informatika', 'code' => 'TI', 'kaprodi_user_id' => $kaprodi->id]);
    $si = Prodi::query()->create(['name' => 'Sistem Informasi', 'code' => 'SI']);

    $dosen = makeKaprodiMonitoringDosen('Dosen Kuota Global', '760001', 2);
    $mahasiswaTi = makeKaprodiMonitoringMahasiswa('Mahasiswa TI Bimbingan', '2309300001', $ti);
    $mahasiswaSi = makeKaprodiMonitoringMahasiswa('Mahasiswa SI Bimbingan', '2309400001', $si);

    Bimbingans::setActiveSupervisor($mahasiswaTi->id, $dosen->id);
    Bimbingans::setActiveSupervisor($mahasiswaSi->id, $dosen->id);

    $this->actingAs($kaprodi);

    Livewire::test(KaprodiBebanDosen::class)
        ->assertSee('Dosen Kuota Global')
        ->assertSee('Penuh')
        ->assertSeeInOrder(['Mahasiswa Prodi', 'Total Aktif'])
        ->assertSeeInOrder(['1', '2']);
});

test('kaprodi mahasiswa perhatian highlights students without supervisor', function () {
    $kaprodi = User::factory()->create([
        'name' => 'Kaprodi TI Perhatian',
        'email' => 'kaprodi.perhatian@sibta.test',
    ]);
    $ti = Prodi::query()->create(['name' => 'Teknik Informatika', 'code' => 'TI', 'kaprodi_user_id' => $kaprodi->id]);

    makeKaprodiMonitoringMahasiswa('Mahasiswa Tanpa Pembimbing', '2309500001', $ti);

    $this->actingAs($kaprodi);

    Livewire::test(KaprodiMahasiswaPerhatian::class)
        ->assertSee('Mahasiswa Tanpa Pembimbing')
        ->assertSee('Belum memiliki dosen pembimbing');
});
