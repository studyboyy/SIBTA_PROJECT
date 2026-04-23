<?php

namespace App\Livewire\Pages;

use App\Models\Mahasiswas;
use App\Support\SidangDocumentCatalog;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class PengelolaanDokumen extends Component
{
    use WithPagination;
    use WithoutUrlPagination;

    public string $searchKelengkapan = '';
    public string $statusKelengkapan = '';
    public int $kelengkapanPerPage = 10;
    public ?int $detailMahasiswaId = null;

    public string $searchArsip = '';
    public string $filterTahun = '';
    public int $arsipPerPage = 10;
    public ?int $previewArsipId = null;

    private function normalize(string $value): string
    {
        return mb_strtolower(trim($value));
    }

    private function resolveStatusKey(string $statusValue): string
    {
        $value = $this->normalize($statusValue);

        return match ($value) {
            'lengkap' => 'lengkap',
            'belum-lengkap', 'belum lengkap' => 'belum-lengkap',
            default => '',
        };
    }

    #[Title('Pengelolaan Dokumen TA')]
    public function lihatDetail(int $mahasiswaId): void
    {
        $this->detailMahasiswaId = $mahasiswaId;
    }

    public function tutupDetail(): void
    {
        $this->detailMahasiswaId = null;
    }

    public function download(string $namaFile): void
    {
        if ($namaFile === '' || $namaFile === '-') {
            session()->flash('error', 'File tidak tersedia.');
            return;
        }

        if (Storage::disk('public')->exists($namaFile)) {
            $filePath = storage_path('app/public/' . $namaFile);
            response()->download($filePath)->send();
            return;
        }

        if (Storage::disk('local')->exists($namaFile)) {
            $filePath = storage_path('app/' . $namaFile);
            response()->download($filePath)->send();
            return;
        }

        session()->flash('error', 'File tidak ditemukan di penyimpanan.');
    }

    public function preview(int $arsipId): void
    {
        $this->previewArsipId = $arsipId;
    }

    public function tutupPreview(): void
    {
        $this->previewArsipId = null;
    }

    public function updatedSearchKelengkapan(): void
    {
        $this->resetPage('kelengkapan');
    }

    public function updatedStatusKelengkapan(): void
    {
        $this->resetPage('kelengkapan');
    }

    public function updatedSearchArsip(): void
    {
        $this->resetPage('arsip');
    }

    public function updatedFilterTahun(): void
    {
        $this->resetPage('arsip');
    }

    public function updatedKelengkapanPerPage($value): void
    {
        $allowed = [5, 10, 15, 20];
        $this->kelengkapanPerPage = in_array((int) $value, $allowed, true) ? (int) $value : 10;
        $this->resetPage('kelengkapan');
    }

    public function updatedArsipPerPage($value): void
    {
        $allowed = [5, 10, 15, 20];
        $this->arsipPerPage = in_array((int) $value, $allowed, true) ? (int) $value : 10;
        $this->resetPage('arsip');
    }

    public function resetFilterKelengkapan(): void
    {
        $this->searchKelengkapan = '';
        $this->statusKelengkapan = '';
        $this->resetPage('kelengkapan');
    }

    public function resetFilterArsip(): void
    {
        $this->searchArsip = '';
        $this->filterTahun = '';
        $this->resetPage('arsip');
    }

    public function render()
    {
        $searchKelengkapan = $this->normalize($this->searchKelengkapan);
        $statusKelengkapan = $this->resolveStatusKey($this->statusKelengkapan);
        $searchArsip = $this->normalize($this->searchArsip);
        $filterTahun = trim($this->filterTahun);

        $kelengkapan = Mahasiswas::with(['user', 'dokumenTa'])
            ->get()
            ->map(function ($mhs) {
                $dokumen = $mhs->dokumenTa;
                $requiredChecklist = SidangDocumentCatalog::checklist($dokumen);
                $checklist = [
                    'Proposal' => $requiredChecklist['proposal'],
                    'Laporan TA' => $requiredChecklist['laporan_ta'],
                    'Jurnal' => $requiredChecklist['jurnal'],
                    'Bebas Lab' => $requiredChecklist['bebas_lab'],
                    'Bebas Pustaka' => $requiredChecklist['bebas_pustaka'],
                ];

                $total = count($checklist);
                $selesai = count(array_filter($checklist));
                $persen = (int) round(($selesai / max(1, $total)) * 100);

                $files = $dokumen
                    ->filter(fn($doc) => (string) $doc->file !== '')
                    ->map(function ($doc) {
                        return [
                            'nama' => (string) $doc->file,
                            'jenis' => trim(SidangDocumentCatalog::label($doc->jenis_dokumen) . ' - ' . (string) ($doc->bab ?: '-'), ' -'),
                            'status' => (string) $doc->status,
                            'catatan' => (string) ($doc->catatan ?: ''),
                        ];
                    })
                    ->values()
                    ->all();

                return [
                    'id' => $mhs->id,
                    'nama' => $mhs->user->name ?? '-',
                    'nim' => $mhs->nim,
                    'checklist' => $checklist,
                    'progress' => $persen,
                    'status_key' => $persen === 100 ? 'lengkap' : 'belum-lengkap',
                    'status_label' => $persen === 100 ? 'Lengkap' : 'Belum lengkap',
                    'files' => $files,
                ];
            });

        // 1) Status filter berdiri sendiri (tetap bekerja walau search kosong)
        if ($statusKelengkapan !== '') {
            $kelengkapan = $kelengkapan
                ->where('status_key', $statusKelengkapan)
                ->values();
        }

        // 2) Search opsional, bisa digabung dengan status filter
        if ($searchKelengkapan !== '') {
            $kelengkapan = $kelengkapan
                ->filter(function ($item) use ($searchKelengkapan) {
                    return str_contains(mb_strtolower((string) $item['nama']), $searchKelengkapan)
                        || str_contains(mb_strtolower((string) $item['nim']), $searchKelengkapan);
                })
                ->values();
        }

        $arsip = Mahasiswas::with(['user', 'dokumenTa'])
            ->where('status_ta', 'Selesai')
            ->get()
            ->map(function ($mhs) {
                $tahun = (int) $mhs->angkatan + 4;
                $finalDoc = $mhs->dokumenTa
                    ->filter(function ($doc) {
                        $bab = strtolower((string) $doc->bab);

                        return str_contains($bab, 'laporan') || str_contains($bab, 'ta') || str_contains($bab, 'jurnal');
                    })
                    ->sortByDesc('updated_at')
                    ->first();

                $judul = 'Tugas Akhir: ' . ($finalDoc?->bab ?: 'Dokumen Final') . ' - ' . ($mhs->user->name ?? 'Mahasiswa');

                return [
                    'id' => $mhs->id,
                    'judul' => $judul,
                    'mahasiswa' => $mhs->user->name ?? '-',
                    'tahun' => (string) $tahun,
                    'keywords' => ['tugas akhir', strtolower((string) $mhs->prodi), 'dokumen final'],
                    'file' => $finalDoc?->file ?: '-',
                ];
            })
            ->filter(function ($item) {
                return $item['file'] !== '-';
            });

        $tahunList = $arsip->pluck('tahun')->unique()->sortDesc()->values();

        $arsip = $arsip
            ->filter(function ($item) use ($searchArsip, $filterTahun) {
                $searchMatch = $searchArsip === ''
                    || str_contains(mb_strtolower((string) $item['judul']), $searchArsip)
                    || str_contains(mb_strtolower((string) $item['mahasiswa']), $searchArsip);

                $tahunMatch = $filterTahun === '' || $item['tahun'] === $filterTahun;

                return $searchMatch && $tahunMatch;
            })
            ->values();

        $detailDokumen = $kelengkapan->firstWhere('id', $this->detailMahasiswaId);
        $previewDokumen = $arsip->firstWhere('id', $this->previewArsipId);

        $kelengkapanPage = $this->getPage('kelengkapan');
        $arsipPage       = $this->getPage('arsip');

        $kelengkapanPaginated = new LengthAwarePaginator(
            $kelengkapan->forPage($kelengkapanPage, $this->kelengkapanPerPage),
            $kelengkapan->count(),
            $this->kelengkapanPerPage,
            $kelengkapanPage,
            ['pageName' => 'kelengkapan'],
        );

        $arsipPaginated = new LengthAwarePaginator(
            $arsip->forPage($arsipPage, $this->arsipPerPage),
            $arsip->count(),
            $this->arsipPerPage,
            $arsipPage,
            ['pageName' => 'arsip'],
        );

        return view('livewire.pages.pengelolaan-dokumen', [
            'kelengkapan' => $kelengkapanPaginated,
            'arsip'       => $arsipPaginated,
            'detailDokumen' => $detailDokumen,
            'previewDokumen' => $previewDokumen,
            'tahunList' => $tahunList,
        ]);
    }
}
