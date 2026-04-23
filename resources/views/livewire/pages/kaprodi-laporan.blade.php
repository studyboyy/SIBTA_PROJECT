<div class="space-y-8">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-emerald-900 to-cyan-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <p class="text-sm font-medium uppercase tracking-[0.3em] text-emerald-100/80">Modul Pimpinan / Kaprodi
                </p>
                <h1 class="text-3xl font-semibold sm:text-4xl">Laporan Komprehensif TA</h1>
                <p class="max-w-2xl text-sm text-emerald-100 sm:text-base">Export laporan TA dan pantau grafik capaian
                    untuk evaluasi akademik.</p>
            </div>
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="tahunFilterLaporan"
                        class="mb-1 block text-xs font-semibold uppercase tracking-wide text-emerald-100/80">Tahun</label>
                    <select id="tahunFilterLaporan" wire:model.live="tahunFilter"
                        class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm text-white focus:border-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-300/40">
                        <option value="" class="text-slate-900">Semua Tahun</option>
                        @foreach ($availableYears as $year)
                            <option value="{{ $year }}" class="text-slate-900">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="semesterFilterLaporan"
                        class="mb-1 block text-xs font-semibold uppercase tracking-wide text-emerald-100/80">Semester</label>
                    <select id="semesterFilterLaporan" wire:model.live="semesterFilter"
                        class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm text-white focus:border-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-300/40">
                        <option value="all" class="text-slate-900">Semua</option>
                        <option value="ganjil" class="text-slate-900">Ganjil</option>
                        <option value="genap" class="text-slate-900">Genap</option>
                    </select>
                </div>
                <button wire:click="exportExcel"
                    class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-emerald-800 shadow-sm hover:bg-emerald-50">
                    Export Excel (CSV)
                </button>
                <a href="{{ route('kaprodi.laporan.pdf') }}" target="_blank"
                    class="rounded-xl border border-white/30 bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/20">
                    Export PDF (Print)
                </a>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-[0.95fr_1.05fr]">
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Grafik Dashboard TA</h2>
            <p class="mt-1 text-sm text-slate-500">Distribusi status penyelesaian untuk pemantauan strategis.</p>

            <div class="mt-6 space-y-4">
                @forelse ($phaseDistribution as $phase)
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <p class="font-medium text-slate-700">{{ $phase['phase'] }}</p>
                            <p class="text-slate-500">{{ $phase['count'] }} mahasiswa ({{ $phase['percentage'] }}%)</p>
                        </div>
                        <div class="h-3 rounded-full bg-slate-100">
                            <div class="h-3 rounded-full bg-linear-to-r from-emerald-500 to-cyan-500"
                                style="width: {{ max($phase['percentage'], 5) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p
                        class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                        Belum ada data grafik TA.</p>
                @endforelse
            </div>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Ringkasan Cepat</h2>
            <div class="mt-4 grid grid-cols-2 gap-4">
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Mahasiswa TA</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $statistik['total_mahasiswa_ta'] }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Selesai Sidang</p>
                    <p class="mt-2 text-2xl font-semibold text-emerald-700">{{ $statistik['selesai_sidang'] }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Avg Durasi Selesai</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $durasi['avg_hari_selesai'] }} hari</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Avg Progres</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $statistik['rata_rata_progres'] }}%</p>
                </div>
            </div>
        </article>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900">Rekap Beban Bimbingan Dosen</h2>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Dosen</th>
                        <th class="px-4 py-3">NIDN</th>
                        <th class="px-4 py-3">Mahasiswa</th>
                        <th class="px-4 py-3">Sesi</th>
                        <th class="px-4 py-3">Kuota</th>
                        <th class="px-4 py-3">Utilisasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($bebanDosen as $row)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $row['name'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['nidn'] ?: '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['total_mahasiswa'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['total_sesi'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['kuota'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['utilisasi'] }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">Belum ada data dosen.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Detail Mahasiswa TA</h2>
                <p class="mt-1 text-sm text-slate-500">Data ini menjadi basis laporan komprehensif TA.</p>
            </div>
            <div>
                <label for="detailPerPage"
                    class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Baris per
                    halaman</label>
                <select id="detailPerPage" wire:model.live="detailPerPage"
                    class="rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Mahasiswa</th>
                        <th class="px-4 py-3">NIM</th>
                        <th class="px-4 py-3">Prodi</th>
                        <th class="px-4 py-3">Fase</th>
                        <th class="px-4 py-3">Progres</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($detailRows as $row)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $row['name'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['nim'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['prodi'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['phase'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['progress'] }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada data mahasiswa
                                TA.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @php
            $currentPage = $this->getPage('detailPage');
            $totalPages = max((int) ceil($totalDetail / $detailPerPage), 1);
        @endphp

        <div class="mt-4 flex items-center justify-between">
            <p class="text-sm text-slate-500">Halaman {{ $currentPage }} dari {{ $totalPages }}
                ({{ $totalDetail }} data)</p>
            <div class="flex gap-2">
                <button wire:click="previousPage('detailPage')" @disabled($currentPage <= 1)
                    class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm text-slate-700 disabled:cursor-not-allowed disabled:opacity-50">
                    Sebelumnya
                </button>
                <button wire:click="nextPage('detailPage')" @disabled($currentPage >= $totalPages)
                    class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm text-slate-700 disabled:cursor-not-allowed disabled:opacity-50">
                    Berikutnya
                </button>
            </div>
        </div>
    </section>
</div>
