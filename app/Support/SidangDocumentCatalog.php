<?php

namespace App\Support;

use Illuminate\Support\Collection;

class SidangDocumentCatalog
{
    /**
     * Daftar jenis dokumen yang tersedia untuk diunggah mahasiswa.
     * Fokus pada dokumen skripsi.
     */
    public const OPTIONS = [
        'proposal'  => 'Dokumen Proposal Skripsi',
        'skripsi'   => 'Dokumen Skripsi (Laporan Akhir)',
        'lainnya'   => 'Dokumen Lainnya',
    ];

    /**
     * Jenis dokumen wajib yang harus disetujui sebelum pengajuan sidang.
     */
    public static function requiredTypes(): array
    {
        return [
            'proposal',
            'skripsi',
        ];
    }

    public static function options(): array
    {
        return self::OPTIONS;
    }

    public static function label(?string $type): string
    {
        return self::OPTIONS[$type ?? ''] ?? 'Dokumen Skripsi';
    }

    public static function isApprovedStatus(?string $status): bool
    {
        return in_array(self::normalize((string) $status), ['approved', 'disetujui'], true);
    }

    public static function normalize(string $value): string
    {
        return mb_strtolower(trim($value));
    }

    public static function matchesType(object $document, string $type): bool
    {
        $jenisDokumen = self::normalize((string) ($document->jenis_dokumen ?? ''));

        if ($jenisDokumen !== '') {
            return $jenisDokumen === $type;
        }

        // Fallback: cocokkan berdasarkan nama bab (untuk data lama)
        $bab = self::normalize((string) ($document->bab ?? ''));

        $keywords = match ($type) {
            'proposal' => ['proposal'],
            'skripsi'  => ['skripsi', 'laporan ta', 'laporan akhir', 'laporan', 'ta'],
            default    => [],
        };

        foreach ($keywords as $keyword) {
            if (str_contains($bab, $keyword)) {
                return true;
            }
        }

        return false;
    }

    public static function checklist(Collection $documents): array
    {
        $result = [];

        foreach (self::requiredTypes() as $type) {
            $result[$type] = $documents->contains(function ($document) use ($type) {
                return self::matchesType($document, $type) && self::isApprovedStatus((string) ($document->status ?? ''));
            });
        }

        return $result;
    }
}
