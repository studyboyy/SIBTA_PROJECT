<?php

use App\Livewire\Pages\MahasiswaChecklistSidang;
use App\Livewire\Pages\MahasiswaJadwalSaya;
use App\Livewire\Pages\MahasiswaPengajuanSidang;
use App\Livewire\Pages\MahasiswaRevisiSaya;
use App\Livewire\Pages\MahasiswaTimelineTa;
use App\Models\BimbinganLog;
use App\Models\Bimbingans;
use App\Models\DokumenTa;
use App\Models\Dosens;
use App\Models\Mahasiswas;
use App\Models\Pengajuanjuduls;
use App\Models\PengajuanSidang;
use App\Models\Prodi;
use App\Models\Sidangs;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

function makeMahasiswaOperationalDosen(string $name, string $nidn): Dosens
{
    $user = User::factory()->create([
        'name' => $name,
        'email' => strtolower(str_replace(' ', '.', $name)).'@mahasiswa-pages.test',
    ]);

    return Dosens::query()->create([
        'user_id' => $user->id,
        'nidn' => $nidn,
        'phone' => $nidn,
        'jabatan' => 'Dosen',
        'kuota_bimbingan' => '10',
    ]);
}

function makeMahasiswaOperationalMahasiswa(string $name, string $nim, Prodi $prodi): Mahasiswas
{
    $user = User::factory()->create([
        'name' => $name,
        'email' => strtolower(str_replace(' ', '.', $name)).'@mahasiswa-operasional.test',
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

test('mahasiswa timeline shows current thesis progress', function () {
    $prodi = Prodi::query()->create(['name' => 'Teknik Informatika', 'code' => 'TI']);
    $dosen = makeMahasiswaOperationalDosen('Dosen Timeline TA', '740001');
    $mahasiswa = makeMahasiswaOperationalMahasiswa('Mahasiswa Timeline TA', '2320100001', $prodi);

    Pengajuanjuduls::query()->create([
        'mahasiswa_id' => $mahasiswa->id,
        'judul' => 'Sistem Informasi Timeline TA',
        'deskripsi' => 'Deskripsi judul',
        'status' => 'approved',
    ]);
    Bimbingans::setActiveSupervisor($mahasiswa->id, $dosen->id);

    $this->actingAs($mahasiswa->user);

    Livewire::test(MahasiswaTimelineTa::class)
        ->assertSee('Sistem Informasi Timeline TA')
        ->assertSee('Dosen Timeline TA')
        ->assertSee('Dokumen Proposal');
});

test('mahasiswa checklist sidang marks required approved documents as ready', function () {
    $prodi = Prodi::query()->create(['name' => 'Teknik Informatika', 'code' => 'TI']);
    $dosen = makeMahasiswaOperationalDosen('Dosen Checklist Sidang', '740002');
    $mahasiswa = makeMahasiswaOperationalMahasiswa('Mahasiswa Checklist Sidang', '2320200001', $prodi);

    Bimbingans::setActiveSupervisor($mahasiswa->id, $dosen->id);

    foreach (['proposal', 'skripsi'] as $type) {
        DokumenTa::query()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'bab' => $type,
            'jenis_dokumen' => $type,
            'file' => 'dokumen-ta/'.$type.'.pdf',
            'status' => 'disetujui',
        ]);
    }

    $this->actingAs($mahasiswa->user);

    Livewire::test(MahasiswaChecklistSidang::class)
        ->assertSee('Siap Ajukan')
        ->assertSee('Dokumen proposal disetujui')
        ->assertSee('Dokumen skripsi disetujui');
});

test('mahasiswa revisi page can resubmit revised document', function () {
    Storage::fake('public');

    $prodi = Prodi::query()->create(['name' => 'Teknik Informatika', 'code' => 'TI']);
    $dosen = makeMahasiswaOperationalDosen('Dosen Revisi Saya', '740003');
    $mahasiswa = makeMahasiswaOperationalMahasiswa('Mahasiswa Revisi Saya', '2320300001', $prodi);

    Bimbingans::setActiveSupervisor($mahasiswa->id, $dosen->id);

    $dokumen = DokumenTa::query()->create([
        'mahasiswa_id' => $mahasiswa->id,
        'bab' => 'Proposal',
        'jenis_dokumen' => 'proposal',
        'file' => 'dokumen-ta/proposal.pdf',
        'status' => 'revisi',
        'catatan' => 'Perbaiki latar belakang.',
    ]);

    $this->actingAs($mahasiswa->user);

    Livewire::test(MahasiswaRevisiSaya::class)
        ->assertSee('Perbaiki latar belakang.')
        ->set('revisiFiles.'.$dokumen->id, UploadedFile::fake()->create('proposal-revisi.pdf', 100, 'application/pdf'))
        ->call('kirimRevisi', $dokumen->id)
        ->assertHasNoErrors();

    $this->assertDatabaseHas('dokumen_ta', [
        'id' => $dokumen->id,
        'status' => 'pending',
    ]);
    $this->assertDatabaseHas('dokumen_ta_versions', [
        'dokumen_ta_id' => $dokumen->id,
        'action' => 'resubmission',
        'status_snapshot' => 'pending',
    ]);
});

test('mahasiswa jadwal saya shows guidance and sidang agenda', function () {
    $prodi = Prodi::query()->create(['name' => 'Teknik Informatika', 'code' => 'TI']);
    $dosen = makeMahasiswaOperationalDosen('Dosen Jadwal Saya', '740004');
    $mahasiswa = makeMahasiswaOperationalMahasiswa('Mahasiswa Jadwal Saya', '2320400001', $prodi);

    Bimbingans::setActiveSupervisor($mahasiswa->id, $dosen->id);

    BimbinganLog::query()->create([
        'mahasiswa_id' => $mahasiswa->id,
        'dosen_id' => $dosen->id,
        'tanggal' => now()->addDays(2)->toDateString(),
        'jam' => '09:00',
        'mode' => 'offline',
        'lokasi' => 'Ruang Dosen',
        'status_sesi' => 'diajukan',
        'konfirmasi_mahasiswa' => 'pending',
    ]);

    Sidangs::query()->create([
        'mahasiswa_id' => $mahasiswa->id,
        'jadwal' => now()->addDays(10)->toDateString(),
        'jam_mulai' => '08:00',
        'jam_selesai' => '10:00',
        'ruangan' => 'Ruang Sidang',
        'status' => 'pending',
    ]);

    $this->actingAs($mahasiswa->user);

    Livewire::test(MahasiswaJadwalSaya::class)
        ->assertSee('Bimbingan offline')
        ->assertSee('Ruang Dosen')
        ->assertSee('Sidang Tugas Akhir')
        ->assertSee('Ruang Sidang');
});

test('mahasiswa cannot resubmit sidang while approval is already in progress', function () {
    $prodi = Prodi::query()->create(['name' => 'Teknik Informatika', 'code' => 'TI']);
    $dosen = makeMahasiswaOperationalDosen('Dosen Approval Berjalan', '740005');
    $mahasiswa = makeMahasiswaOperationalMahasiswa('Mahasiswa Approval Berjalan', '2320500001', $prodi);

    Bimbingans::setActiveSupervisor($mahasiswa->id, $dosen->id);

    foreach (['proposal', 'skripsi'] as $type) {
        DokumenTa::query()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'bab' => $type,
            'jenis_dokumen' => $type,
            'file' => 'dokumen-ta/'.$type.'.pdf',
            'status' => 'disetujui',
        ]);
    }

    $pengajuan = PengajuanSidang::query()->create([
        'mahasiswa_id' => $mahasiswa->id,
        'status' => 'pending',
        'status_dosen' => 'approved',
        'status_kaprodi' => 'approved',
        'catatan_mahasiswa' => 'Pengajuan pertama',
        'diajukan_pada' => now(),
    ]);

    $this->actingAs($mahasiswa->user);

    Livewire::test(MahasiswaPengajuanSidang::class)
        ->set('catatan_mahasiswa', 'Pengajuan ulang yang tidak boleh menyentuh approval')
        ->call('submit')
        ->assertHasNoErrors();

    expect($pengajuan->refresh())
        ->status_dosen->toBe('approved')
        ->status_kaprodi->toBe('approved')
        ->catatan_mahasiswa->toBe('Pengajuan pertama');
});
