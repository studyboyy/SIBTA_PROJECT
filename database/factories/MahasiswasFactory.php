<?php

namespace Database\Factories;

use App\Models\Mahasiswas;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends Factory<Mahasiswas>
 */
class MahasiswasFactory extends Factory
{
    protected $model = Mahasiswas::class;

    public function definition(): array
    {
        $faker = fake('id_ID');
        $name = $faker->name();
        $prodi = Prodi::query()
            ->whereIn('name', ['Teknik Informatika', 'Sistem Informasi'])
            ->inRandomOrder()
            ->first()
            ?? Prodi::query()->firstOrCreate(
                ['name' => 'Teknik Informatika'],
                ['code' => 'TI']
            );

        return [
            'user_id' => User::factory()->indonesian()->withPassword('Mahasiswa123!'),
            'nim' => $faker->unique()->numerify('23########'),
            'angkatan' => $faker->randomElement(['2021', '2022', '2023', '2024']),
            'prodi' => $prodi->name,
            'prodi_id' => $prodi->id,
            'status_ta' => Mahasiswas::STATUS_TA_PENDING,
            'photo' => $this->avatarPath($name, 'avatar_mahasiswa', '#0f766e'),
        ];
    }

    public function forProdi(Prodi $prodi): static
    {
        return $this->state(fn (array $attributes) => [
            'prodi' => $prodi->name,
            'prodi_id' => $prodi->id,
        ]);
    }

    private function avatarPath(string $name, string $directory, string $background): string
    {
        $initials = collect(explode(' ', trim($name)))
            ->filter()
            ->take(2)
            ->map(fn (string $part) => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');

        $initials = $initials !== '' ? $initials : 'M';
        $filename = Str::slug($name).'-'.Str::lower(Str::random(6)).'.svg';
        $path = $directory.'/'.$filename;

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="300" height="300" viewBox="0 0 300 300">
  <rect width="300" height="300" fill="{$background}" />
  <text x="50%" y="52%" dominant-baseline="middle" text-anchor="middle" font-family="Arial, sans-serif" font-size="96" font-weight="700" fill="#ffffff">{$initials}</text>
</svg>
SVG;

        Storage::disk('public')->put($path, $svg);

        return $path;
    }
}
