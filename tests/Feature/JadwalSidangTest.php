<?php

use App\Livewire\Pages\JadwalSidang;
use App\Models\Bimbingans;
use App\Models\Dosens;
use App\Models\Mahasiswas;
use App\Models\PengajuanSidang;
use App\Models\Prodi;
use App\Models\SidangBatch;
use App\Models\Sidangs;
use App\Models\User;
use Livewire\Livewire;

function makeJadwalSidangDosen(string $name, string $nidn): Dosens
{
    $user = User::factory()->create([
        'name' => $name,
        'email' => strtolower(str_replace(' ', '.', $name)).'@sidang.test',
    ]);

    return Dosens::query()->create([
        'user_id' => $user->id,
        'nidn' => $nidn,
        'phone' => $nidn,
        'jabatan' => 'Dosen',
        'kuota_bimbingan' => '10',
    ]);
}

function makeJadwalSidangMahasiswa(string $name, string $nim, Prodi $prodi): Mahasiswas
{
    $user = User::factory()->create([
        'name' => $name,
        'email' => strtolower(str_replace(' ', '.', $name)).'@mahasiswa.test',
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

test('admin can create a sidang batch without selecting prodi', function () {
    $admin = User::factory()->create();
    $ketua = makeJadwalSidangDosen('Ketua Sidang', '880001');
    $penguji1 = makeJadwalSidangDosen('Penguji Satu', '880002');
    $penguji2 = makeJadwalSidangDosen('Penguji Dua', '880003');

    $this->actingAs($admin);

    Livewire::test(JadwalSidang::class)
        ->set('tanggal', '2026-06-20')
        ->set('jam_mulai', '08:00')
        ->set('jam_selesai', '10:00')
        ->set('ruangan', 'Ruang Sidang 1')
        ->set('gelombang', '1')
        ->set('kuotaPerGelombang', 20)
        ->set('ketua_sidang_id', (string) $ketua->id)
        ->set('penguji_1_id', (string) $penguji1->id)
        ->set('penguji_2_id', (string) $penguji2->id)
        ->call('simpan')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('sidang_batches', [
        'tanggal' => '2026-06-20',
        'ruangan' => 'Ruang Sidang 1',
        'prodi_id' => null,
    ]);
});

test('admin schedules approved sidang requests into any available batch across prodi', function () {
    $admin = User::factory()->create();
    $ti = Prodi::query()->create(['name' => 'Teknik Informatika', 'code' => 'TI']);
    $si = Prodi::query()->create(['name' => 'Sistem Informasi', 'code' => 'SI']);

    $ketua = makeJadwalSidangDosen('Ketua Lintas Prodi', '880004');
    $penguji1 = makeJadwalSidangDosen('Penguji Lintas Satu', '880005');
    $penguji2 = makeJadwalSidangDosen('Penguji Lintas Dua', '880006');

    $batch = SidangBatch::query()->create([
        'tanggal' => '2026-06-21',
        'jam_mulai' => '08:00',
        'jam_selesai' => '10:00',
        'ruangan' => 'Ruang Sidang Bersama',
        'gelombang' => 1,
        'kuota' => 2,
        'prodi_id' => $ti->id,
        'ketua_sidang_id' => $ketua->id,
        'penguji_1_id' => $penguji1->id,
        'penguji_2_id' => $penguji2->id,
    ]);

    $mahasiswaTi = makeJadwalSidangMahasiswa('Mahasiswa TI', '2301000001', $ti);
    $mahasiswaSi = makeJadwalSidangMahasiswa('Mahasiswa SI', '2302000001', $si);

    $pengajuanTi = PengajuanSidang::query()->create([
        'mahasiswa_id' => $mahasiswaTi->id,
        'status' => 'pending',
        'status_dosen' => 'approved',
        'status_kaprodi' => 'approved',
        'diajukan_pada' => now(),
    ]);

    $pengajuanSi = PengajuanSidang::query()->create([
        'mahasiswa_id' => $mahasiswaSi->id,
        'status' => 'pending',
        'status_dosen' => 'approved',
        'status_kaprodi' => 'approved',
        'diajukan_pada' => now(),
    ]);

    $this->actingAs($admin);

    Livewire::test(JadwalSidang::class)
        ->call('approvePengajuan', $pengajuanTi->id)
        ->call('approvePengajuan', $pengajuanSi->id)
        ->assertHasNoErrors();

    $this->assertDatabaseHas('sidangs', [
        'mahasiswa_id' => $mahasiswaTi->id,
        'sidang_batch_id' => $batch->id,
        'gelombang' => 1,
    ]);
    $this->assertDatabaseHas('sidangs', [
        'mahasiswa_id' => $mahasiswaSi->id,
        'sidang_batch_id' => $batch->id,
        'gelombang' => 1,
    ]);
    $this->assertDatabaseHas('pengajuan_sidangs', [
        'id' => $pengajuanSi->id,
        'status' => 'approved',
        'gelombang' => 1,
    ]);

    $sidangTi = Sidangs::query()->where('mahasiswa_id', $mahasiswaTi->id)->firstOrFail();

    expect($mahasiswaTi->refresh()->status_ta)->toBe(Mahasiswas::STATUS_TA_PENDING);

    Livewire::test(JadwalSidang::class)
        ->call('updateSidangStatus', $sidangTi->id, Sidangs::STATUS_LULUS)
        ->assertHasNoErrors();

    expect($mahasiswaTi->refresh()->status_ta)->toBe(Mahasiswas::STATUS_TA_SELESAI);
});

test('daftar mahasiswa tab shows supervised students with eligibility status', function () {
    $admin = User::factory()->create();
    $kaprodi = User::factory()->create([
        'name' => 'Kaprodi TI',
        'email' => 'kaprodi.daftar.sidang@sibta.test',
    ]);
    $prodi = Prodi::query()->create([
        'name' => 'Teknik Informatika',
        'code' => 'TI',
        'kaprodi_user_id' => $kaprodi->id,
    ]);

    $pembimbing = makeJadwalSidangDosen('Dosen Pembimbing Sidang', '880007');
    $mahasiswaBelumLayak = makeJadwalSidangMahasiswa('A Mahasiswa Belum Layak', '2303000001', $prodi);
    $mahasiswaLayak = makeJadwalSidangMahasiswa('Z Mahasiswa Layak', '2303000002', $prodi);

    Bimbingans::setActiveSupervisor($mahasiswaBelumLayak->id, $pembimbing->id);
    Bimbingans::setActiveSupervisor($mahasiswaLayak->id, $pembimbing->id);

    PengajuanSidang::query()->create([
        'mahasiswa_id' => $mahasiswaLayak->id,
        'status' => 'pending',
        'status_dosen' => 'approved',
        'status_kaprodi' => 'pending',
        'diajukan_pada' => now(),
    ]);

    $this->actingAs($admin);

    Livewire::test(JadwalSidang::class)
        ->call('setActiveTab', 'sidang')
        ->assertSee('Z Mahasiswa Layak')
        ->assertSee('A Mahasiswa Belum Layak')
        ->assertSee('Dosen Pembimbing Sidang')
        ->assertSee('Kaprodi TI')
        ->assertSee('Layak Sidang')
        ->assertSee('Belum Layak')
        ->assertSeeInOrder(['Z Mahasiswa Layak', 'A Mahasiswa Belum Layak']);
});
