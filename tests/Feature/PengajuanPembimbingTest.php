<?php

use App\Livewire\Pages\KaprodiPengajuanJudul;
use App\Models\Bimbingans;
use App\Models\Dosens;
use App\Models\Mahasiswas;
use App\Models\PengajuanPembimbing;
use App\Models\Prodi;
use App\Models\User;
use Livewire\Livewire;

function makeDosen(string $name, string $nidn, int $kuota = 2): Dosens
{
    $user = User::factory()->create([
        'name' => $name,
        'email' => strtolower(str_replace(' ', '.', $name)).'@sibta.test',
    ]);

    return Dosens::query()->create([
        'user_id' => $user->id,
        'nidn' => $nidn,
        'phone' => $nidn,
        'jabatan' => 'Dosen',
        'kuota_bimbingan' => (string) $kuota,
    ]);
}

test('pengajuan pembimbing uses the singular table from the migration', function () {
    $dosen = makeDosen('Dosen Tujuan', '990001');
    $mahasiswaUser = User::factory()->create();
    $mahasiswa = Mahasiswas::query()->create([
        'user_id' => $mahasiswaUser->id,
        'nim' => '2300000001',
        'angkatan' => '2023',
        'prodi' => 'Teknik Informatika',
        'status_ta' => 'Pending',
    ]);

    PengajuanPembimbing::query()->create([
        'mahasiswa_id' => $mahasiswa->id,
        'dosen_id' => $dosen->id,
        'alasan' => 'Ingin mengganti pembimbing.',
        'status' => 'pending',
        'diajukan_pada' => now(),
    ]);

    $this->assertDatabaseHas('pengajuan_pembimbing', [
        'mahasiswa_id' => $mahasiswa->id,
        'dosen_id' => $dosen->id,
        'status' => 'pending',
    ]);
});

test('kaprodi approval replaces the active supervisor assignment', function () {
    $kaprodi = User::factory()->create([
        'name' => 'Kaprodi TI',
        'email' => 'kaprodi.ti@sibta.test',
    ]);
    $prodi = Prodi::query()->create([
        'name' => 'Teknik Informatika',
        'code' => 'TI',
        'kaprodi_user_id' => $kaprodi->id,
    ]);

    $oldDosen = makeDosen('Dosen Lama', '990002');
    $newDosen = makeDosen('Dosen Baru', '990003');
    $mahasiswaUser = User::factory()->create();
    $mahasiswa = Mahasiswas::query()->create([
        'user_id' => $mahasiswaUser->id,
        'nim' => '2300000002',
        'angkatan' => '2023',
        'prodi' => $prodi->name,
        'prodi_id' => $prodi->id,
        'status_ta' => 'Pending',
    ]);

    Bimbingans::setActiveSupervisor($mahasiswa->id, $oldDosen->id);

    $pengajuan = PengajuanPembimbing::query()->create([
        'mahasiswa_id' => $mahasiswa->id,
        'dosen_id' => $newDosen->id,
        'alasan' => 'Keahlian dosen lebih sesuai topik.',
        'status' => 'pending',
        'diajukan_pada' => now(),
    ]);

    $this->actingAs($kaprodi);

    Livewire::test(KaprodiPengajuanJudul::class)
        ->set('actionPembimbingId', $pengajuan->id)
        ->set('actionType', 'approved')
        ->call('prosesAksiPembimbing')
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('bimbingans', [
        'mahasiswa_id' => $mahasiswa->id,
        'dosen_id' => $oldDosen->id,
    ]);
    $this->assertDatabaseHas('bimbingans', [
        'mahasiswa_id' => $mahasiswa->id,
        'dosen_id' => $newDosen->id,
        'status' => 'aktif',
    ]);
    $this->assertDatabaseHas('pengajuan_pembimbing', [
        'id' => $pengajuan->id,
        'status' => 'approved',
        'diproses_oleh' => $kaprodi->id,
    ]);
});
