<div class="space-y-8">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-cyan-900 to-emerald-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <p class="text-sm font-medium uppercase tracking-[0.3em] text-cyan-100/80">Modul Pimpinan / Kaprodi</p>
                <h1 class="text-3xl font-semibold sm:text-4xl">Dashboard Monitoring TA</h1>
                <p class="max-w-2xl text-sm text-cyan-100 sm:text-base">
                    Pantau statistik mahasiswa tugas akhir, lama waktu penyelesaian, dan distribusi beban dosen
                    pembimbing.
                </p>
                <div
                    class="inline-flex rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-50 backdrop-blur">
                    Cakupan data: {{ $managedProdi?->name ?? 'Semua program studi' }}
                </div>
            </div>
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="tahunFilterDashboard"
                        class="mb-1 block text-xs font-semibold uppercase tracking-wide text-cyan-100/80">Tahun</label>
                    <select id="tahunFilterDashboard" wire:model.live="tahunFilter"
                        class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm text-white focus:border-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-300/40">
                        <option value="" class="text-slate-900">Semua Tahun</option>
                        @foreach ($availableYears as $year)
                            <option value="{{ $year }}" class="text-slate-900">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="semesterFilterDashboard"
                        class="mb-1 block text-xs font-semibold uppercase tracking-wide text-cyan-100/80">Semester</label>
                    <select id="semesterFilterDashboard" wire:model.live="semesterFilter"
                        class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm text-white focus:border-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-300/40">
                        <option value="all" class="text-slate-900">Semua</option>
                        <option value="ganjil" class="text-slate-900">Ganjil</option>
                        <option value="genap" class="text-slate-900">Genap</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-cyan-100/70">Mahasiswa TA</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $statistik['total_mahasiswa_ta'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-cyan-100/70">Siap Sidang</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $statistik['siap_sidang'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-cyan-100/70">Selesai Sidang</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $statistik['selesai_sidang'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-cyan-100/70">Rata-rata Progres</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $statistik['rata_rata_progres'] }}%</p>
                </div>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Lama Waktu Penyelesaian TA</h2>
            <p class="mt-1 text-sm text-slate-500">Estimasi durasi dihitung dari awal pengajuan judul hingga sidang
                selesai.</p>

            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Rata-rata Selesai</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $durasi['avg_hari_selesai'] }} hari</p>
                    <p class="mt-1 text-xs text-slate-500">Dari {{ $durasi['total_mahasiswa_selesai'] }} mahasiswa yang
                        sudah sidang.</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Rata-rata Berjalan</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $durasi['avg_hari_berjalan'] }} hari</p>
                    <p class="mt-1 text-xs text-slate-500">Estimasi durasi mahasiswa yang masih proses TA.</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Durasi Tersingkat</p>
                    <p class="mt-2 text-2xl font-semibold text-emerald-700">{{ $durasi['min_hari_selesai'] }} hari</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Durasi Terlama</p>
                    <p class="mt-2 text-2xl font-semibold text-amber-700">{{ $durasi['max_hari_selesai'] }} hari</p>
                </div>
            </div>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Grafik Status TA</h2>
            <p class="mt-1 text-sm text-slate-500">Distribusi fase penyelesaian tugas akhir mahasiswa.</p>

            <div class="mt-6 space-y-4">
                @forelse ($phaseDistribution as $phase)
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <p class="font-medium text-slate-700">{{ $phase['phase'] }}</p>
                            <p class="text-slate-500">{{ $phase['count'] }} mahasiswa ({{ $phase['percentage'] }}%)
                            </p>
                        </div>
                        <div class="h-3 rounded-full bg-slate-100">
                            <div class="h-3 rounded-full bg-linear-to-r from-cyan-600 to-emerald-500"
                                style="width: {{ max($phase['percentage'], 5) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p
                        class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                        Belum ada data distribusi fase TA.
                    </p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Beban Bimbingan Dosen</h2>
                <p class="text-sm text-slate-500">Top dosen berdasarkan jumlah mahasiswa bimbingan aktif.</p>
            </div>
            <a href="{{ route('kaprodi.laporan') }}" wire:navigate class="text-sm font-semibold text-cyan-700">Buka
                laporan komprehensif</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Dosen</th>
                        <th class="px-4 py-3">Mahasiswa Bimbingan</th>
                        <th class="px-4 py-3">Total Sesi</th>
                        <th class="px-4 py-3">Kuota</th>
                        <th class="px-4 py-3">Utilisasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($bebanDosen as $row)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $row['name'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['total_mahasiswa'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['total_sesi'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['kuota'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['utilisasi'] }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada data beban dosen.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
