<?php

namespace App\Support;

use App\Models\Mahasiswas;
use App\Models\Sidangs;
use Illuminate\Support\Collection;

class MahasiswaProgressSummary
{
    public function summarizeCollection(Collection $mahasiswas): Collection
    {
        return $mahasiswas->map(fn(Mahasiswas $mahasiswa) => $this->summarize($mahasiswa));
    }

    public function summarize(Mahasiswas $mahasiswa): array
    {
        $documents = $mahasiswa->dokumenTa;
        $requiredChecklist = SidangDocumentCatalog::checklist($documents);
        $requiredTotal = count(SidangDocumentCatalog::requiredTypes());
        $approvedRequiredDocuments = count(array_filter($requiredChecklist));
        $isRequiredDocumentsComplete = $approvedRequiredDocuments === $requiredTotal;
        $approvedDocuments = $documents->filter(fn($document) => $this->normalizeStatus($document->status) === 'approved')->count();
        $pendingDocuments = $documents->filter(fn($document) => $this->normalizeStatus($document->status) === 'pending')->count();
        $rejectedDocuments = $documents->filter(fn($document) => $this->normalizeStatus($document->status) === 'rejected')->count();
        $bimbinganCount = $mahasiswa->relationLoaded('bimbinganLogs')
            ? $mahasiswa->bimbinganLogs->count()
            : $mahasiswa->bimbingans->count();
        $sidang = $mahasiswa->sidang;

        $hasBimbingan = $mahasiswa->bimbingans->isNotEmpty();
        $hasSidang = $sidang !== null;
        $progress = $this->calculateProgress(
            $requiredTotal,
            $approvedRequiredDocuments,
            $hasBimbingan,
            $hasSidang,
            $isRequiredDocumentsComplete,
        );
        $phase = $this->determinePhase(
            $mahasiswa->status_ta,
            $requiredTotal,
            $approvedRequiredDocuments,
            $rejectedDocuments,
            $hasBimbingan,
            $hasSidang,
            $sidang?->status,
        );

        $reasons = [];
        $urgencyScore = 0;

        if (! $hasBimbingan) {
            $reasons[] = 'Belum memiliki dosen pembimbing';
            $urgencyScore += 4;
        }

        if ($rejectedDocuments > 0) {
            $reasons[] = $rejectedDocuments . ' dokumen ditolak';
            $urgencyScore += 3;
        }

        if ($pendingDocuments > 0) {
            $reasons[] = $pendingDocuments . ' dokumen menunggu review';
            $urgencyScore += 2;
        }

        if ($isRequiredDocumentsComplete && ! $hasSidang) {
            $reasons[] = 'Sudah mendekati sidang tetapi jadwal belum dibuat';
            $urgencyScore += 1;
        }

        $latestBimbingan = $mahasiswa->relationLoaded('bimbinganLogs')
            ? $mahasiswa->bimbinganLogs->sortByDesc('tanggal')->first()
            : $mahasiswa->bimbingans->sortByDesc('tanggal')->first();
        $supervisorNames = $mahasiswa->bimbingans
            ->map(fn($bimbingan) => $bimbingan->dosen?->user?->name)
            ->filter()
            ->unique()
            ->values();

        return [
            'id' => $mahasiswa->id,
            'name' => $mahasiswa->user?->name ?? 'Mahasiswa',
            'nim' => $mahasiswa->nim,
            'prodi' => $mahasiswa->prodi,
            'status_ta' => $mahasiswa->status_ta,
            'phase' => $phase,
            'progress' => $progress,
            'documents_count' => $documents->count(),
            'approved_documents' => $approvedDocuments,
            'pending_documents' => $pendingDocuments,
            'rejected_documents' => $rejectedDocuments,
            'bimbingan_count' => $bimbinganCount,
            'has_bimbingan' => $hasBimbingan,
            'has_sidang' => $hasSidang,
            'is_ready_for_sidang' => ! $hasSidang && $isRequiredDocumentsComplete,
            'supervisors' => $supervisorNames,
            'latest_bimbingan' => $latestBimbingan?->tanggal,
            'sidang_date' => $sidang?->jadwal,
            'sidang_status' => $sidang?->status,
            'urgency_score' => $urgencyScore,
            'reasons' => $reasons,
        ];
    }

    private function calculateProgress(int $documentCount, int $approvedDocuments, bool $hasBimbingan, bool $hasSidang, bool $isRequiredDocumentsComplete): int
    {
        if ($isRequiredDocumentsComplete) {
            return 100;
        }

        $progress = 10;

        if ($hasBimbingan) {
            $progress += 35;
        }

        if ($documentCount > 0) {
            $progress += (int) round(($approvedDocuments / $documentCount) * 35);
        }

        if ($hasSidang) {
            $progress += 20;
        }

        return min($progress, 100);
    }

    private function determinePhase(
        string $statusTa,
        int $documentCount,
        int $approvedDocuments,
        int $rejectedDocuments,
        bool $hasBimbingan,
        bool $hasSidang,
        ?string $sidangStatus,
    ): string {
        if ($hasSidang && Sidangs::isCompletedStatus($sidangStatus)) {
            return 'Selesai Sidang';
        }

        if ($hasSidang) {
            return 'Terjadwal Sidang';
        }

        if ($hasBimbingan && $documentCount > 0 && $approvedDocuments === $documentCount && $rejectedDocuments === 0) {
            return 'Siap Sidang';
        }

        if ($hasBimbingan) {
            return 'Proses Bimbingan';
        }

        if ($documentCount > 0) {
            return 'Validasi Dokumen';
        }

        return $this->formatStatusTa($statusTa);
    }

    private function normalizeStatus(?string $status): string
    {
        if ($status === null) {
            return 'unknown';
        }

        $normalized = strtolower(trim($status));

        return match ($normalized) {
            'approved', 'disetujui', 'selesai', 'lulus' => 'approved',
            'rejected', 'ditolak', 'revisi' => 'rejected',
            'pending', 'menunggu', 'proses' => 'pending',
            default => $normalized,
        };
    }

    private function formatStatusTa(?string $statusTa): string
    {
        if (! $statusTa) {
            return 'Belum Memulai';
        }

        return collect(explode('_', str_replace('-', '_', strtolower($statusTa))))
            ->filter()
            ->map(fn(string $part) => ucfirst($part))
            ->implode(' ');
    }
}
