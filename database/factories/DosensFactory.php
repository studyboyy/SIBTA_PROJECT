<?php

namespace Database\Factories;

use App\Models\Dosens;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends Factory<Dosens>
 */
class DosensFactory extends Factory
{
    protected $model = Dosens::class;

    public function definition(): array
    {
        $faker = fake('id_ID');
        $name = $faker->name();

        return [
            'user_id' => User::factory()->indonesian()->withPassword('Dosen123!'),
            'nidn' => $faker->unique()->numerify('00########'),
            'phone' => '08'.$faker->unique()->numerify('##########'),
            'jabatan' => $faker->randomElement(['Asisten Ahli', 'Lektor', 'Lektor Kepala']),
            'kuota_bimbingan' => (string) $faker->numberBetween(6, 12),
            'photo' => $this->avatarPath($name, 'avatar_dosen', '#1d4ed8'),
        ];
    }

    private function avatarPath(string $name, string $directory, string $background): string
    {
        $initials = collect(explode(' ', trim($name)))
            ->filter()
            ->take(2)
            ->map(fn (string $part) => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');

        $initials = $initials !== '' ? $initials : 'D';
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
