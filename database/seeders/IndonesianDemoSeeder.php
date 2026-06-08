<?php

namespace Database\Seeders;

use App\Models\Bimbingans;
use App\Models\Dosens;
use App\Models\Mahasiswas;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IndonesianDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $this->createBaseAccounts();
        $prodis = $this->createProdisWithKaprodi();
        $dosens = $this->createDosens();
        $this->createMahasiswas($prodis, $dosens);
    }

    private function createBaseAccounts(): void
    {
        $this->upsertUser(
            email: 'admin@sibta.test',
            name: 'Admin SIBTA',
            password: 'Admin123!',
            role: 'admin',
            color: '#0f172a',
        );

        $this->upsertUser(
            email: 'pimpinan@sibta.test',
            name: 'Pimpinan SIBTA',
            password: 'Pimpinan123!',
            role: 'pimpinan',
            color: '#7c3aed',
        );
    }

    private function createProdisWithKaprodi(): array
    {
        $kaprodiRows = [
            'TI' => [
                'name' => 'Dr. Raka Pratama, M.Kom.',
                'email' => 'kaprodi.ti@sibta.test',
                'password' => 'Kaprodi123!',
                'prodi' => 'Teknik Informatika',
                'color' => '#0369a1',
            ],
            'SI' => [
                'name' => 'Dr. Dian Kusuma, M.Kom.',
                'email' => 'kaprodi.si@sibta.test',
                'password' => 'Kaprodi123!',
                'prodi' => 'Sistem Informasi',
                'color' => '#0f766e',
            ],
        ];

        $prodis = [];

        foreach ($kaprodiRows as $code => $row) {
            $kaprodi = $this->upsertUser(
                email: $row['email'],
                name: $row['name'],
                password: $row['password'],
                role: 'kaprodi',
                color: $row['color'],
            );

            $prodis[$code] = Prodi::query()->updateOrCreate(
                ['name' => $row['prodi']],
                [
                    'code' => $code,
                    'kaprodi_user_id' => $kaprodi->id,
                ],
            );
        }

        return $prodis;
    }

    private function createDosens(): array
    {
        $rows = [
            ['name' => 'Dr. Budi Santoso, M.Kom.', 'email' => 'dosen.budi@sibta.test', 'nidn' => '0010018501', 'phone' => '081200100001', 'jabatan' => 'Lektor Kepala', 'kuota' => 8],
            ['name' => 'Dr. Siti Nur Aisyah, M.Cs.', 'email' => 'dosen.siti@sibta.test', 'nidn' => '0010028602', 'phone' => '081200100002', 'jabatan' => 'Lektor Kepala', 'kuota' => 8],
            ['name' => 'Ahmad Fauzi, M.Kom.', 'email' => 'dosen.ahmad@sibta.test', 'nidn' => '0010038703', 'phone' => '081200100003', 'jabatan' => 'Lektor', 'kuota' => 10],
            ['name' => 'Rina Kartika, M.T.', 'email' => 'dosen.rina@sibta.test', 'nidn' => '0010048804', 'phone' => '081200100004', 'jabatan' => 'Lektor', 'kuota' => 10],
            ['name' => 'Hendra Wijaya, M.Cs.', 'email' => 'dosen.hendra@sibta.test', 'nidn' => '0010058905', 'phone' => '081200100005', 'jabatan' => 'Lektor', 'kuota' => 10],
            ['name' => 'Maya Puspitasari, M.Kom.', 'email' => 'dosen.maya@sibta.test', 'nidn' => '0010069006', 'phone' => '081200100006', 'jabatan' => 'Asisten Ahli', 'kuota' => 6],
            ['name' => 'Agus Setiawan, M.T.', 'email' => 'dosen.agus@sibta.test', 'nidn' => '0010079107', 'phone' => '081200100007', 'jabatan' => 'Lektor', 'kuota' => 10],
            ['name' => 'Nurul Hidayati, M.Kom.', 'email' => 'dosen.nurul@sibta.test', 'nidn' => '0010089208', 'phone' => '081200100008', 'jabatan' => 'Asisten Ahli', 'kuota' => 6],
            ['name' => 'Dedi Kurniawan, M.Cs.', 'email' => 'dosen.dedi@sibta.test', 'nidn' => '0010099309', 'phone' => '081200100009', 'jabatan' => 'Lektor', 'kuota' => 10],
            ['name' => 'Lestari Wulandari, M.T.', 'email' => 'dosen.lestari@sibta.test', 'nidn' => '0010109400', 'phone' => '081200100010', 'jabatan' => 'Asisten Ahli', 'kuota' => 6],
        ];

        $dosens = [];

        foreach ($rows as $row) {
            $user = $this->upsertUser(
                email: $row['email'],
                name: $row['name'],
                password: 'Dosen123!',
                role: 'dosen',
                color: '#1d4ed8',
            );

            $photo = $this->avatarPath($row['name'], 'avatar_dosen', '#1d4ed8');
            $dosen = Dosens::query()->where('nidn', $row['nidn'])->first();

            if (! $dosen) {
                $dosen = Dosens::factory()->create([
                    'user_id' => $user->id,
                    'nidn' => $row['nidn'],
                    'phone' => $row['phone'],
                    'jabatan' => $row['jabatan'],
                    'kuota_bimbingan' => (string) $row['kuota'],
                    'photo' => $photo,
                ]);
            } else {
                $dosen->update([
                    'user_id' => $user->id,
                    'phone' => $row['phone'],
                    'jabatan' => $row['jabatan'],
                    'kuota_bimbingan' => (string) $row['kuota'],
                    'photo' => $photo,
                ]);
            }

            $dosens[] = $dosen->refresh();
        }

        return $dosens;
    }

    private function createMahasiswas(array $prodis, array $dosens): void
    {
        $rows = [
            ['name' => 'Andi Prasetyo', 'email' => 'mhs.ti.001@sibta.test', 'nim' => '2310100001', 'prodi' => 'TI', 'angkatan' => '2023'],
            ['name' => 'Bima Setiawan', 'email' => 'mhs.ti.002@sibta.test', 'nim' => '2310100002', 'prodi' => 'TI', 'angkatan' => '2023'],
            ['name' => 'Citra Lestari', 'email' => 'mhs.ti.003@sibta.test', 'nim' => '2310100003', 'prodi' => 'TI', 'angkatan' => '2023'],
            ['name' => 'Dewi Anggraini', 'email' => 'mhs.ti.004@sibta.test', 'nim' => '2310100004', 'prodi' => 'TI', 'angkatan' => '2023'],
            ['name' => 'Eko Saputra', 'email' => 'mhs.ti.005@sibta.test', 'nim' => '2310100005', 'prodi' => 'TI', 'angkatan' => '2023'],
            ['name' => 'Fajar Nugroho', 'email' => 'mhs.ti.006@sibta.test', 'nim' => '2310100006', 'prodi' => 'TI', 'angkatan' => '2023'],
            ['name' => 'Galih Ramadhan', 'email' => 'mhs.ti.007@sibta.test', 'nim' => '2310100007', 'prodi' => 'TI', 'angkatan' => '2023'],
            ['name' => 'Hana Maharani', 'email' => 'mhs.ti.008@sibta.test', 'nim' => '2310100008', 'prodi' => 'TI', 'angkatan' => '2023'],
            ['name' => 'Indra Wijaya', 'email' => 'mhs.ti.009@sibta.test', 'nim' => '2310100009', 'prodi' => 'TI', 'angkatan' => '2023'],
            ['name' => 'Joko Firmansyah', 'email' => 'mhs.ti.010@sibta.test', 'nim' => '2310100010', 'prodi' => 'TI', 'angkatan' => '2023'],
            ['name' => 'Kartika Sari', 'email' => 'mhs.ti.011@sibta.test', 'nim' => '2310100011', 'prodi' => 'TI', 'angkatan' => '2023'],
            ['name' => 'Laila Fitriani', 'email' => 'mhs.ti.012@sibta.test', 'nim' => '2310100012', 'prodi' => 'TI', 'angkatan' => '2023'],
            ['name' => 'Rizky Maulana', 'email' => 'mhs.ti.013@sibta.test', 'nim' => '2310100013', 'prodi' => 'TI', 'angkatan' => '2023'],
            ['name' => 'Nadia Putri', 'email' => 'mhs.ti.014@sibta.test', 'nim' => '2310100014', 'prodi' => 'TI', 'angkatan' => '2023'],
            ['name' => 'Oka Wibowo', 'email' => 'mhs.ti.015@sibta.test', 'nim' => '2310100015', 'prodi' => 'TI', 'angkatan' => '2023'],
            ['name' => 'Putri Aulia', 'email' => 'mhs.si.001@sibta.test', 'nim' => '2320200001', 'prodi' => 'SI', 'angkatan' => '2023'],
            ['name' => 'Reza Fahlevi', 'email' => 'mhs.si.002@sibta.test', 'nim' => '2320200002', 'prodi' => 'SI', 'angkatan' => '2023'],
            ['name' => 'Salsabila Rahma', 'email' => 'mhs.si.003@sibta.test', 'nim' => '2320200003', 'prodi' => 'SI', 'angkatan' => '2023'],
            ['name' => 'Taufik Hidayat', 'email' => 'mhs.si.004@sibta.test', 'nim' => '2320200004', 'prodi' => 'SI', 'angkatan' => '2023'],
            ['name' => 'Vina Aprilia', 'email' => 'mhs.si.005@sibta.test', 'nim' => '2320200005', 'prodi' => 'SI', 'angkatan' => '2023'],
            ['name' => 'Wahyu Kurniawan', 'email' => 'mhs.si.006@sibta.test', 'nim' => '2320200006', 'prodi' => 'SI', 'angkatan' => '2023'],
            ['name' => 'Yuni Astuti', 'email' => 'mhs.si.007@sibta.test', 'nim' => '2320200007', 'prodi' => 'SI', 'angkatan' => '2023'],
            ['name' => 'Zaki Alfarizi', 'email' => 'mhs.si.008@sibta.test', 'nim' => '2320200008', 'prodi' => 'SI', 'angkatan' => '2023'],
            ['name' => 'Alya Nabila', 'email' => 'mhs.si.009@sibta.test', 'nim' => '2320200009', 'prodi' => 'SI', 'angkatan' => '2023'],
            ['name' => 'Dimas Prakoso', 'email' => 'mhs.si.010@sibta.test', 'nim' => '2320200010', 'prodi' => 'SI', 'angkatan' => '2023'],
            ['name' => 'Eka Wulandari', 'email' => 'mhs.si.011@sibta.test', 'nim' => '2320200011', 'prodi' => 'SI', 'angkatan' => '2023'],
            ['name' => 'Farhan Hakim', 'email' => 'mhs.si.012@sibta.test', 'nim' => '2320200012', 'prodi' => 'SI', 'angkatan' => '2023'],
            ['name' => 'Gisella Permata', 'email' => 'mhs.si.013@sibta.test', 'nim' => '2320200013', 'prodi' => 'SI', 'angkatan' => '2023'],
            ['name' => 'Hendra Gunawan', 'email' => 'mhs.si.014@sibta.test', 'nim' => '2320200014', 'prodi' => 'SI', 'angkatan' => '2023'],
            ['name' => 'Intan Permatasari', 'email' => 'mhs.si.015@sibta.test', 'nim' => '2320200015', 'prodi' => 'SI', 'angkatan' => '2023'],
        ];

        foreach ($rows as $index => $row) {
            $prodi = $prodis[$row['prodi']];
            $user = $this->upsertUser(
                email: $row['email'],
                name: $row['name'],
                password: 'Mahasiswa123!',
                role: 'mahasiswa',
                color: $row['prodi'] === 'TI' ? '#0f766e' : '#c2410c',
            );

            $photo = $this->avatarPath($row['name'], 'avatar_mahasiswa', $row['prodi'] === 'TI' ? '#0f766e' : '#c2410c');
            $mahasiswa = Mahasiswas::query()->where('nim', $row['nim'])->first();

            if (! $mahasiswa) {
                $mahasiswa = Mahasiswas::factory()
                    ->forProdi($prodi)
                    ->create([
                        'user_id' => $user->id,
                        'nim' => $row['nim'],
                        'angkatan' => $row['angkatan'],
                        'status_ta' => Mahasiswas::STATUS_TA_PENDING,
                        'photo' => $photo,
                    ]);
            } else {
                $mahasiswa->update([
                    'user_id' => $user->id,
                    'angkatan' => $row['angkatan'],
                    'prodi' => $prodi->name,
                    'prodi_id' => $prodi->id,
                    'status_ta' => Mahasiswas::STATUS_TA_PENDING,
                    'photo' => $photo,
                ]);
            }

            $dosen = $dosens[$index % count($dosens)];
            Bimbingans::setActiveSupervisor($mahasiswa->id, $dosen->id);
        }
    }

    private function upsertUser(string $email, string $name, string $password, string $role, string $color): User
    {
        $photo = $this->avatarPath($name, 'avatar_users', $color);
        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            $user = User::factory()
                ->indonesian()
                ->withPassword($password)
                ->create([
                    'name' => $name,
                    'email' => $email,
                    'photo' => $photo,
                    'email_verified_at' => now(),
                ]);
        } else {
            $user->forceFill([
                'name' => $name,
                'password' => Hash::make($password),
                'photo' => $photo,
                'email_verified_at' => now(),
            ])->save();
        }

        $user->syncRoles([$role]);

        return $user;
    }

    private function avatarPath(string $name, string $directory, string $background): string
    {
        $initials = collect(explode(' ', trim(preg_replace('/[^A-Za-z ]/', ' ', $name) ?? $name)))
            ->filter()
            ->take(2)
            ->map(fn (string $part) => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');

        $initials = $initials !== '' ? $initials : 'U';
        $filename = Str::slug($name).'.svg';
        $path = $directory.'/'.$filename;

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="300" height="300" viewBox="0 0 300 300">
  <rect width="300" height="300" fill="{$background}" />
  <text x="50%" y="52%" dominant-baseline="middle" text-anchor="middle" font-family="Arial, sans-serif" font-size="88" font-weight="700" fill="#ffffff">{$initials}</text>
</svg>
SVG;

        Storage::disk('public')->put($path, $svg);

        return $path;
    }
}
