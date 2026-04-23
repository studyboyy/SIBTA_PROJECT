<?php

namespace App\Livewire\Pages;

use App\Support\KaprodiReportSummary;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class KaprodiLaporan extends Component
{
    use WithPagination;

    public int $detailPerPage = 10;
    public string $tahunFilter = '';
    public string $semesterFilter = 'all';

    #[Title('Laporan Kaprodi')]
    public function render()
    {
        $year = $this->tahunFilter !== '' ? (int) $this->tahunFilter : null;
        $summary = app(KaprodiReportSummary::class)->build($year, $this->semesterFilter, Auth::user());

        $detailRows = $summary['progress_rows']
            ->sortByDesc('progress')
            ->values()
            ->forPage($this->getPage('detailPage'), $this->detailPerPage)
            ->values();

        $totalDetail = $summary['progress_rows']->count();

        return view('livewire.pages.kaprodi-laporan', [
            'statistik' => $summary['statistik'],
            'durasi' => $summary['durasi'],
            'bebanDosen' => $summary['beban_dosen'],
            'phaseDistribution' => $summary['phase_distribution'],
            'detailRows' => $detailRows,
            'totalDetail' => $totalDetail,
            'availableYears' => $summary['available_years'],
        ]);
    }

    public function updatedDetailPerPage($value): void
    {
        $allowed = [10, 25, 50];
        $this->detailPerPage = in_array((int) $value, $allowed, true) ? (int) $value : 10;
        $this->resetPage('detailPage');
    }

    public function updatedTahunFilter(): void
    {
        $this->resetPage('detailPage');
    }

    public function updatedSemesterFilter(): void
    {
        $this->resetPage('detailPage');
    }

    public function exportExcel()
    {
        $year = $this->tahunFilter !== '' ? (int) $this->tahunFilter : null;
        $summary = app(KaprodiReportSummary::class)->build($year, $this->semesterFilter, Auth::user());

        $filename = 'laporan-ta-kaprodi-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($summary) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Laporan TA Kaprodi']);
            fputcsv($handle, ['Tanggal Export', now()->format('d-m-Y H:i:s')]);
            fputcsv($handle, []);

            fputcsv($handle, ['Ringkasan Statistik']);
            fputcsv($handle, ['Total Mahasiswa TA', $summary['statistik']['total_mahasiswa_ta']]);
            fputcsv($handle, ['Siap Sidang', $summary['statistik']['siap_sidang']]);
            fputcsv($handle, ['Selesai Sidang', $summary['statistik']['selesai_sidang']]);
            fputcsv($handle, ['Masih Proses', $summary['statistik']['masih_proses']]);
            fputcsv($handle, ['Rata-rata Progres (%)', $summary['statistik']['rata_rata_progres']]);
            fputcsv($handle, []);

            fputcsv($handle, ['Beban Bimbingan Dosen']);
            fputcsv($handle, ['Nama Dosen', 'NIDN', 'Mahasiswa Bimbingan', 'Relasi Bimbingan', 'Total Sesi', 'Kehadiran', 'Kuota', 'Utilisasi (%)']);
            foreach ($summary['beban_dosen'] as $row) {
                fputcsv($handle, [
                    $row['name'],
                    $row['nidn'] ?: '-',
                    $row['total_mahasiswa'],
                    $row['total_relasi'],
                    $row['total_sesi'],
                    $row['total_hadir'],
                    $row['kuota'],
                    $row['utilisasi'],
                ]);
            }
            fputcsv($handle, []);

            fputcsv($handle, ['Detail Progres Mahasiswa']);
            fputcsv($handle, ['Nama', 'NIM', 'Prodi', 'Fase', 'Progres (%)', 'Siap Sidang']);
            foreach ($summary['progress_rows'] as $row) {
                fputcsv($handle, [
                    $row['name'],
                    $row['nim'],
                    $row['prodi'],
                    $row['phase'],
                    $row['progress'],
                    $row['is_ready_for_sidang'] ? 'Ya' : 'Tidak',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
