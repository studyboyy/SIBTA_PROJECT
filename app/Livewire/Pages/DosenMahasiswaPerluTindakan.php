<?php

namespace App\Livewire\Pages;

use App\Livewire\Pages\Concerns\UsesDosenScope;
use App\Support\MahasiswaProgressSummary;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

class DosenMahasiswaPerluTindakan extends Component
{
    use UsesDosenScope;

    public string $search = '';

    public string $kategori = '';

    #[Title('Mahasiswa Perlu Tindakan')]
    public function render()
    {
        $dosen = $this->getDosen();

        $rows = app(MahasiswaProgressSummary::class)
            ->summarizeCollection(
                $this->mahasiswaBimbinganQuery($dosen)
                    ->with(['user', 'dokumenTa', 'bimbingans.dosen.user', 'bimbinganLogs', 'pengajuanSidang', 'sidang'])
                    ->get()
            )
            ->map(fn (array $row) => $this->addDosenActionSignals($row))
            ->filter(fn (array $row) => $row['action_score'] > 0)
            ->when($this->kategori !== '', fn ($rows) => $rows->filter(fn (array $row) => $this->matchesKategori($row)))
            ->when($this->search !== '', function ($rows) {
                $search = str($this->search)->lower()->toString();

                return $rows->filter(function (array $row) use ($search) {
                    return str_contains(strtolower($row['name']), $search)
                        || str_contains(strtolower($row['nim']), $search)
                        || str_contains(strtolower($row['prodi']), $search);
                });
            })
            ->sortByDesc('action_score')
            ->values();

        return view('livewire.pages.dosen-mahasiswa-perlu-tindakan', [
            'rows' => $rows,
            'summary' => [
                'total' => $rows->count(),
                'dokumen_pending' => $rows->filter(fn (array $row) => $row['pending_documents'] > 0)->count(),
                'dokumen_ditolak' => $rows->filter(fn (array $row) => $row['rejected_documents'] > 0)->count(),
                'sidang_pending' => $rows->where('sidang_action_pending', true)->count(),
                'lama_tidak_bimbingan' => $rows->where('long_without_guidance', true)->count(),
            ],
        ]);
    }

    private function addDosenActionSignals(array $row): array
    {
        $reasons = collect($row['reasons'] ?? []);
        $score = (int) ($row['urgency_score'] ?? 0);
        $latestBimbingan = $row['latest_bimbingan'] ? Carbon::parse($row['latest_bimbingan']) : null;
        $longWithoutGuidance = ! $latestBimbingan || $latestBimbingan->lt(now()->subDays(30));
        $sidangPending = in_array($row['sidang_status'], ['pending', null, ''], true) && $row['is_ready_for_sidang'];

        if ($longWithoutGuidance) {
            $reasons->push('Belum ada bimbingan terbaru lebih dari 30 hari');
            $score += 2;
        }

        if ($sidangPending) {
            $reasons->push('Perlu keputusan kelayakan sidang');
            $score += 3;
        }

        if ($row['pending_documents'] > 0) {
            $score += 2;
        }

        if ($row['rejected_documents'] > 0) {
            $score += 3;
        }

        $row['action_score'] = $score;
        $row['action_reasons'] = $reasons->unique()->values();
        $row['long_without_guidance'] = $longWithoutGuidance;
        $row['sidang_action_pending'] = $sidangPending;

        return $row;
    }

    private function matchesKategori(array $row): bool
    {
        return match ($this->kategori) {
            'dokumen_pending' => $row['pending_documents'] > 0,
            'dokumen_ditolak' => $row['rejected_documents'] > 0,
            'sidang_pending' => $row['sidang_action_pending'],
            'lama_tidak_bimbingan' => $row['long_without_guidance'],
            default => true,
        };
    }
}
