<?php

use App\Livewire\Pages\DosenBimbinganLog;
use App\Livewire\Pages\DosenKontrolBimbingan;
use App\Livewire\Pages\DosenKelayakanSidang;
use App\Livewire\Pages\DosenMahasiswaPerluTindakan;
use App\Livewire\Pages\DosenMonitoringMahasiswa;
use App\Livewire\Pages\DosenReviewDokumen;
use App\Models\Bimbingans;
use App\Models\DokumenTa;
use App\Models\Dosens;
use App\Models\Mahasiswas;
use App\Models\PengajuanSidang;
use App\Models\Prodi;
use App\Models\User;
use Livewire\Livewire;

function makeDosenOperationalDosen(string $name, string $nidn): Dosens
{
    $user = User::factory()->create([
        'name' => $name,
        'email' => strtolower(str_replace(' ', '.', $name)).'@dosen-pages.test',
    ]);

    return Dosens::query()->create([
        'user_id' => $user->id,
        'nidn' => $nidn,
        'phone' => $nidn,
        'jabatan' => 'Dosen',
        'kuota_bimbingan' => '10',
    ]);
}

function makeDosenOperationalMahasiswa(string $name, string $nim, Prodi $prodi): Mahasiswas
{
    $user = User::factory()->create([
        'name' => $name,
        'email' => strtolower(str_replace(' ', '.', $name)).'@dosen-mahasiswa.test',
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

test('dosen monitoring mahasiswa only shows supervised students', function () {
    $prodi = Prodi::query()->create(['name' => 'Teknik Informatika', 'code' => 'TI']);
    $dosen = makeDosenOperationalDosen('Dosen Monitoring Operasional', '750001');
    $mahasiswaBimbingan = makeDosenOperationalMahasiswa('Mahasiswa Bimbingan Dosen', '2310100001', $prodi);
    $mahasiswaLain = makeDosenOperationalMahasiswa('Mahasiswa Bukan Bimbingan', '2310100002', $prodi);

    Bimbingans::setActiveSupervisor($mahasiswaBimbingan->id, $dosen->id);

    $this->actingAs($dosen->user);

    Livewire::test(DosenMonitoringMahasiswa::class)
        ->assertSee('Mahasiswa Bimbingan Dosen')
        ->assertDontSee('Mahasiswa Bukan Bimbingan');
});

test('dosen review dokumen can approve owned student document', function () {
    $prodi = Prodi::query()->create(['name' => 'Teknik Informatika', 'code' => 'TI']);
    $dosen = makeDosenOperationalDosen('Dosen Review Dokumen', '750002');
    $mahasiswa = makeDosenOperationalMahasiswa('Mahasiswa Dokumen Review', '2310200001', $prodi);

    Bimbingans::setActiveSupervisor($mahasiswa->id, $dosen->id);

    $dokumen = DokumenTa::query()->create([
        'mahasiswa_id' => $mahasiswa->id,
        'bab' => 'Proposal',
        'jenis_dokumen' => 'proposal',
        'file' => 'dokumen-ta/proposal.pdf',
        'status' => 'pending',
    ]);

    $this->actingAs($dosen->user);

    Livewire::test(DosenReviewDokumen::class)
        ->assertSee('Mahasiswa Dokumen Review')
        ->call('setDokumenStatus', $dokumen->id, 'disetujui')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('dokumen_ta', [
        'id' => $dokumen->id,
        'status' => 'disetujui',
    ]);
});

test('dosen kelayakan sidang approves only after required documents are complete', function () {
    $prodi = Prodi::query()->create(['name' => 'Teknik Informatika', 'code' => 'TI']);
    $dosen = makeDosenOperationalDosen('Dosen Kelayakan Sidang', '750003');
    $mahasiswa = makeDosenOperationalMahasiswa('Mahasiswa Siap Sidang', '2310300001', $prodi);

    Bimbingans::setActiveSupervisor($mahasiswa->id, $dosen->id);

    $pengajuan = PengajuanSidang::query()->create([
        'mahasiswa_id' => $mahasiswa->id,
        'status' => 'pending',
        'status_dosen' => 'pending',
        'status_kaprodi' => 'pending',
        'diajukan_pada' => now(),
    ]);

    foreach (['proposal', 'skripsi'] as $type) {
        DokumenTa::query()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'bab' => $type,
            'jenis_dokumen' => $type,
            'file' => 'dokumen-ta/'.$type.'.pdf',
            'status' => 'disetujui',
        ]);
    }

    $this->actingAs($dosen->user);

    Livewire::test(DosenKelayakanSidang::class)
        ->assertSee('Mahasiswa Siap Sidang')
        ->call('updateSidangStatus', $pengajuan->id, 'approved')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('pengajuan_sidangs', [
        'id' => $pengajuan->id,
        'status_dosen' => 'approved',
    ]);
});

test('legacy kontrol bimbingan also blocks sidang approval before required documents are complete', function () {
    $prodi = Prodi::query()->create(['name' => 'Teknik Informatika', 'code' => 'TI']);
    $dosen = makeDosenOperationalDosen('Dosen Kontrol Legacy', '750005');
    $mahasiswa = makeDosenOperationalMahasiswa('Mahasiswa Legacy Sidang', '2310500001', $prodi);

    Bimbingans::setActiveSupervisor($mahasiswa->id, $dosen->id);

    $pengajuan = PengajuanSidang::query()->create([
        'mahasiswa_id' => $mahasiswa->id,
        'status' => 'pending',
        'status_dosen' => 'pending',
        'status_kaprodi' => 'pending',
        'diajukan_pada' => now(),
    ]);

    $this->actingAs($dosen->user);

    Livewire::test(DosenKontrolBimbingan::class)
        ->call('updateSidangStatus', $pengajuan->id, 'approved')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('pengajuan_sidangs', [
        'id' => $pengajuan->id,
        'status_dosen' => 'pending',
    ]);
});

test('dosen bimbingan online saves meeting link into link online field', function () {
    $prodi = Prodi::query()->create(['name' => 'Teknik Informatika', 'code' => 'TI']);
    $dosen = makeDosenOperationalDosen('Dosen Link Online', '750006');
    $mahasiswa = makeDosenOperationalMahasiswa('Mahasiswa Link Online', '2310600001', $prodi);

    Bimbingans::setActiveSupervisor($mahasiswa->id, $dosen->id);

    $this->actingAs($dosen->user);

    Livewire::test(DosenBimbinganLog::class)
        ->set('mode', 'online')
        ->assertSeeHtml('wire:model="link_online"')
        ->set('tanggal', '2026-06-18')
        ->set('jam', '09:30')
        ->set('link_online', 'https://meet.example.test/ta')
        ->set('catatan', 'Bimbingan online')
        ->call('simpan')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('bimbingan_logs', [
        'mahasiswa_id' => $mahasiswa->id,
        'dosen_id' => $dosen->id,
        'mode' => 'online',
        'lokasi' => null,
        'link_online' => 'https://meet.example.test/ta',
    ]);
});

test('dosen mahasiswa perlu tindakan highlights pending documents', function () {
    $prodi = Prodi::query()->create(['name' => 'Teknik Informatika', 'code' => 'TI']);
    $dosen = makeDosenOperationalDosen('Dosen Tindakan Mahasiswa', '750004');
    $mahasiswa = makeDosenOperationalMahasiswa('Mahasiswa Dokumen Pending', '2310400001', $prodi);

    Bimbingans::setActiveSupervisor($mahasiswa->id, $dosen->id);

    DokumenTa::query()->create([
        'mahasiswa_id' => $mahasiswa->id,
        'bab' => 'Proposal',
        'jenis_dokumen' => 'proposal',
        'file' => 'dokumen-ta/proposal.pdf',
        'status' => 'pending',
    ]);

    $this->actingAs($dosen->user);

    Livewire::test(DosenMahasiswaPerluTindakan::class)
        ->assertSee('Mahasiswa Dokumen Pending')
        ->assertSee('dokumen menunggu review');
});
