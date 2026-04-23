<?php

namespace App\Support;

use Illuminate\Support\Collection;

class SidangDocumentCatalog
{
    public const OPTIONS = [
        'proposal' => 'Dokumen Proposal',
        'laporan_ta' => 'Dokumen Laporan TA',
        'jurnal' => 'Dokumen Jurnal',
        'bebas_lab' => 'Dokumen Bebas Lab',
        'bebas_pustaka' => 'Dokumen Bebas Pustaka',
        'lainnya' => 'Dokumen Lainnya',
    ];

    public static function options(): array
    {
        return self::OPTIONS;
    }

    public static function requiredTypes(): array
    {
        return [
            'proposal',
            'laporan_ta',
            'jurnal',
            'bebas_lab',
            'bebas_pustaka',
        ];
    }

    public static function label(?string $type): string
    {
        return self::OPTIONS[$type ?? ''] ?? 'Dokumen TA';
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

        $bab = self::normalize((string) ($document->bab ?? ''));

        $keywords = match ($type) {
            'proposal' => ['proposal'],
            'laporan_ta' => ['laporan ta', 'laporan', 'ta'],
            'jurnal' => ['jurnal'],
            'bebas_lab' => ['bebas lab', 'lab'],
            'bebas_pustaka' => ['bebas pustaka', 'pustaka'],
            default => [],
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
