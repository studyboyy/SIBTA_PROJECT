<?php

use App\Models\Bimbingans;
use App\Models\Dosens;
use App\Models\Mahasiswas;
use App\Models\PengajuanSidang;
use App\Models\Sidangs;
use App\Models\User;

function makeStatusTaDosen(): Dosens
{
    $user = User::factory()->create();

    return Dosens::query()->create([
        'user_id' => $user->id,
        'nidn' => '770001',
        'phone' => '770001',
        'jabatan' => 'Dosen',
        'kuota_bimbingan' => '10',
    ]);
}

function makeStatusTaMahasiswa(string $nim = '2309000001'): Mahasiswas
{
    $user = User::factory()->create();

    return Mahasiswas::query()->create([
        'user_id' => $user->id,
        'nim' => $nim,
        'angkatan' => '2023',
        'prodi' => 'Teknik Informatika',
        'status_ta' => Mahasiswas::STATUS_TA_SELESAI,
    ]);
}

test('status ta is pending when mahasiswa has no supervisor and no approved sidang', function () {
    $mahasiswa = makeStatusTaMahasiswa();

    $mahasiswa->syncStatusTa();

    expect($mahasiswa->refresh()->status_ta)->toBe(Mahasiswas::STATUS_TA_PENDING);
});

test('status ta becomes proses when supervisor is assigned and returns pending when removed', function () {
    $dosen = makeStatusTaDosen();
    $mahasiswa = makeStatusTaMahasiswa();

    Bimbingans::setActiveSupervisor($mahasiswa->id, $dosen->id);

    expect($mahasiswa->refresh()->status_ta)->toBe(Mahasiswas::STATUS_TA_PROSES);

    Bimbingans::query()->where('mahasiswa_id', $mahasiswa->id)->firstOrFail()->delete();

    expect($mahasiswa->refresh()->status_ta)->toBe(Mahasiswas::STATUS_TA_PENDING);
});

test('status ta stays proses when sidang request is approved but sidang is not final', function () {
    $dosen = makeStatusTaDosen();
    $mahasiswa = makeStatusTaMahasiswa();

    Bimbingans::setActiveSupervisor($mahasiswa->id, $dosen->id);

    PengajuanSidang::query()->create([
        'mahasiswa_id' => $mahasiswa->id,
        'status' => 'approved',
        'status_dosen' => 'approved',
        'status_kaprodi' => 'approved',
        'diajukan_pada' => now(),
    ]);

    expect($mahasiswa->refresh()->status_ta)->toBe(Mahasiswas::STATUS_TA_PROSES);

    Sidangs::query()->create([
        'mahasiswa_id' => $mahasiswa->id,
        'jadwal' => now()->addDays(7)->toDateString(),
        'ruangan' => 'Ruang Sidang',
        'status' => Sidangs::STATUS_PENDING,
    ]);

    expect($mahasiswa->refresh()->status_ta)->toBe(Mahasiswas::STATUS_TA_PROSES);
});

test('status ta becomes selesai when sidang status is final', function () {
    $dosen = makeStatusTaDosen();
    $mahasiswa = makeStatusTaMahasiswa();

    Bimbingans::setActiveSupervisor($mahasiswa->id, $dosen->id);

    $sidang = Sidangs::query()->create([
        'mahasiswa_id' => $mahasiswa->id,
        'jadwal' => now()->addDays(7)->toDateString(),
        'ruangan' => 'Ruang Sidang',
        'status' => Sidangs::STATUS_PENDING,
    ]);

    expect($mahasiswa->refresh()->status_ta)->toBe(Mahasiswas::STATUS_TA_PROSES);

    $sidang->update(['status' => Sidangs::STATUS_LULUS]);

    expect($mahasiswa->refresh()->status_ta)->toBe(Mahasiswas::STATUS_TA_SELESAI);

    $sidang->update(['status' => Sidangs::STATUS_TIDAK_LULUS]);

    expect($mahasiswa->refresh()->status_ta)->toBe(Mahasiswas::STATUS_TA_PROSES);
});
