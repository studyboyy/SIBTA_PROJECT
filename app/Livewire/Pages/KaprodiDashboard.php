<?php

namespace App\Livewire\Pages;

use App\Support\KaprodiReportSummary;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

class KaprodiDashboard extends Component
{
    public string $tahunFilter = '';
    public string $semesterFilter = 'all';

    #[Title('Dashboard Kaprodi')]
    public function render()
    {
        $year = $this->tahunFilter !== '' ? (int) $this->tahunFilter : null;
        $summary = app(KaprodiReportSummary::class)->build($year, $this->semesterFilter, Auth::user());

        return view('livewire.pages.kaprodi-dashboard', [
            'statistik' => $summary['statistik'],
            'durasi' => $summary['durasi'],
            'bebanDosen' => $summary['beban_dosen']->take(8),
            'phaseDistribution' => $summary['phase_distribution'],
            'availableYears' => $summary['available_years'],
            'managedProdi' => Auth::user()?->managedProdi,
        ]);
    }
}
