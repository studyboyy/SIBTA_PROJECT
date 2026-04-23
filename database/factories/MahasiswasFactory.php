<?php

namespace Database\Factories;

use App\Models\Mahasiswas;
use App\Models\Prodi;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends Factory<Mahasiswas>
 */
class MahasiswasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->name();
        $baseFilename = now()->format('YmdHis') . '_' . Str::slug($name) . '_' . Str::lower(Str::random(6));
        $filename = $baseFilename . '.jpg';

        $avatarId = random_int(1, 70);
        $response = null;

        try {
            $response = Http::withOptions(['verify' => false])->timeout(15)->retry(2, 250)->get("https://i.pravatar.cc/300?img={$avatarId}");
        } catch (ConnectionException $e) {
            $response = null;
        }

        if ($response && $response->successful() && str_contains((string) $response->header('Content-Type'), 'image/')) {
            Storage::disk('public')->put('avatar_users/' . $filename, $response->body());
        } else {
            $filename = $baseFilename . '.svg';
            $initial = Str::upper(Str::substr($name, 0, 1));
            $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="300" height="300" viewBox="0 0 300 300">
  <rect width="300" height="300" fill="#0ea5e9" />
  <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Arial, sans-serif" font-size="120" fill="#ffffff">{$initial}</text>
</svg>
SVG;

            Storage::disk('public')->put('avatar_users/' . $filename, $svg);
        }

        $prodi = Prodi::query()->whereIn('name', ['Teknik Informatika', 'Sistem Informasi'])->inRandomOrder()->first();

        if (! $prodi) {
            $prodi = Prodi::firstOrCreate(
                ['name' => 'Teknik Informatika'],
                ['code' => 'TI']
            );
        }

        return [
            "nim" => $this->faker->unique()->numerify('##########'),
            "angkatan" => $this->faker->randomElement(['2019', '2020', '2021', '2022']),
            "prodi" => $prodi->name,
            "prodi_id" => $prodi->id,
            "status_ta" => $this->faker->randomElement(['Pending', 'Proses', 'Selesai']),
            "photo" => 'avatar_users/' . $filename,
        ];
    }
}
