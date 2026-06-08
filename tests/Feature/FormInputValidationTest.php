<?php

use App\Livewire\Pages\Dosen as DosenPage;
use App\Livewire\Pages\Mahasiswa as MahasiswaPage;
use App\Models\Prodi;
use Illuminate\Support\Facades\Validator;
use Livewire\Livewire;

test('mahasiswa form rejects text in numeric academic fields', function () {
    $prodi = Prodi::query()->create([
        'name' => 'Teknik Informatika',
        'code' => 'TI',
    ]);

    Livewire::test(MahasiswaPage::class)
        ->set('name', 'Mahasiswa Validasi')
        ->set('email', 'mahasiswa.validasi@example.test')
        ->set('nim', 'NIM-ABC')
        ->set('angkatan', 'dua ribu')
        ->set('prodi_id', (string) $prodi->id)
        ->call('store')
        ->assertHasErrors([
            'nim' => 'regex',
            'angkatan' => 'digits',
        ]);
});

test('dosen form rejects text in numeric identity fields', function () {
    Livewire::test(DosenPage::class)
        ->set('name', 'Dosen Validasi')
        ->set('email', 'dosen.validasi@example.test')
        ->set('nidn', 'NIDN-ABC')
        ->set('jabatan', 'Lektor')
        ->set('phone', '08ABC123')
        ->set('kuota_bimbingan', 'sepuluh')
        ->call('store')
        ->assertHasErrors([
            'nidn' => 'regex',
            'phone' => 'regex',
            'kuota_bimbingan' => 'integer',
        ]);
});

test('default validation messages are shown in Indonesian', function () {
    $validator = Validator::make([
        'name' => '',
        'email' => 'bukan-email',
    ], [
        'name' => 'required',
        'email' => 'email',
    ]);

    expect(app()->getLocale())->toBe('id')
        ->and($validator->errors()->first('name'))->toBe('Kolom nama lengkap wajib diisi.')
        ->and($validator->errors()->first('email'))->toBe('Kolom email harus berupa alamat email yang valid.');
});
