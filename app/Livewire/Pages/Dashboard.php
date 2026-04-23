<?php

namespace App\Livewire\Pages;

use App\Models\Dosens;
use App\Models\Mahasiswas;
use App\Models\Sidangs;
use App\Support\MahasiswaProgressSummary;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

class Dashboard extends Component
{
    #[Title('Dashboard')]
    public $greeting;
    public $users;
    public $role;

    public function mount()
    {
        $this->users = Auth::user();
        $this->role = $this->users->roles->first()->name;
        $hour = now()->hour;
        if ($hour < 12) {
            $greeting = 'Selamat Pagi';
        } elseif ($hour < 15) {
            $greeting = 'Selamat Siang';
        } elseif ($hour < 18) {
            $greeting = 'Selamat Sore';
        } else {
            $greeting = 'Selamat Malam';
        }
        $this->greeting = $greeting;
    }

    public function render()
    {
        $mahasiswas = Mahasiswas::query()
            ->with(['user', 'dokumenTa', 'bimbingans.dosen.user', 'bimbinganLogs', 'sidang'])
            ->get();

        $progressRows = app(MahasiswaProgressSummary::class)->summarizeCollection($mahasiswas);

        $summary = [
            'totalMahasiswa' => $mahasiswas->count(),
            'totalDosen' => Dosens::count(),
            'aktifBimbingan' => $progressRows->where('has_bimbingan', true)->count(),
            'siapSidang' => $progressRows->where('is_ready_for_sidang', true)->count(),
            'sidangTerjadwal' => Sidangs::query()->whereDate('jadwal', '>=', now()->toDateString())->count(),
            'rataRataProgres' => (int) round($progressRows->avg('progress') ?? 0),
        ];

        $statusDistribution = $progressRows
            ->groupBy('phase')
            ->map(fn(Collection $items, string $phase) => [
                'label' => $phase,
                'count' => $items->count(),
                'percentage' => $summary['totalMahasiswa'] > 0
                    ? (int) round(($items->count() / $summary['totalMahasiswa']) * 100)
                    : 0,
            ])
            ->sortByDesc('count')
            ->values();

        $urgentMahasiswa = $progressRows
            ->filter(fn(array $row) => $row['urgency_score'] > 0)
            ->sortByDesc('urgency_score')
            ->take(6)
            ->values();

        $siapSidang = $progressRows
            ->filter(fn(array $row) => $row['is_ready_for_sidang'])
            ->sortByDesc('progress')
            ->take(6)
            ->values();

        $upcomingSidangs = Sidangs::query()
            ->with(['mahasiswa.user', 'ketuaSidang.user', 'penguji1.user', 'penguji2.user'])
            ->whereDate('jadwal', '>=', now()->toDateString())
            ->orderBy('jadwal')
            ->orderBy('jam_mulai')
            ->take(5)
            ->get();

        return view('livewire.pages.dashboard', [
            'summary' => $summary,
            'statusDistribution' => $statusDistribution,
            'urgentMahasiswa' => $urgentMahasiswa,
            'siapSidang' => $siapSidang,
            'upcomingSidangs' => $upcomingSidangs,
        ]);
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect(route('login'));
    }
}
