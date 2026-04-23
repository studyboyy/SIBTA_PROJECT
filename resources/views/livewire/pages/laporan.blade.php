<div class="space-y-8">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-blue-900 to-cyan-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <p class="text-sm font-medium uppercase tracking-[0.3em] text-cyan-100/80">Laporan Admin</p>
                <h1 class="text-3xl font-semibold sm:text-4xl">Rekap Tugas Akhir</h1>
                <p class="max-w-2xl text-sm text-blue-100 sm:text-base">
                    Ringkasan data mahasiswa, beban dosen pembimbing, dan status penyelesaian TA untuk pemantauan
                    berkala.
                </p>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-cyan-100/70">Total Mahasiswa TA</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $completionSummary['total'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-cyan-100/70">Rata-rata Progres</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $completionSummary['rata_rata_progres'] }}%</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-cyan-100/70">Siap Sidang</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $completionSummary['siap_sidang'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-cyan-100/70">Sudah Sidang</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $completionSummary['sudah_sidang'] }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Rekap Mahasiswa TA per Semester</h2>
                <p class="text-sm text-slate-500">Pembagian semester akademik dihitung otomatis dari tanggal data
                    mahasiswa dibuat.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Semester</th>
                        <th class="px-4 py-3">Total Mahasiswa</th>
                        <th class="px-4 py-3">Siap Sidang</th>
                        <th class="px-4 py-3">Terjadwal Sidang</th>
                        <th class="px-4 py-3">Selesai Sidang</th>
                        <th class="px-4 py-3">Rata-rata Progres</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($semesterRows as $row)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $row['semester'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['total_mahasiswa'] }}</td>
                            <td class="px-4 py-3 text-emerald-700">{{ $row['siap_sidang'] }}</td>
                            <td class="px-4 py-3 text-blue-700">{{ $row['terjadwal_sidang'] }}</td>
                            <td class="px-4 py-3 text-indigo-700">{{ $row['selesai_sidang'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['rata_rata_progres'] }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">Belum ada data mahasiswa
                                untuk direkap.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Rekap Beban Bimbingan Dosen</h2>
                <p class="text-sm text-slate-500">Memantau distribusi mahasiswa bimbingan, total sesi, dan utilisasi
                    kuota dosen.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Dosen</th>
                        <th class="px-4 py-3">NIDN</th>
                        <th class="px-4 py-3">Mahasiswa Bimbingan</th>
                        <th class="px-4 py-3">Relasi Bimbingan</th>
                        <th class="px-4 py-3">Total Sesi</th>
                        <th class="px-4 py-3">Kehadiran Terkonfirmasi</th>
                        <th class="px-4 py-3">Kuota</th>
                        <th class="px-4 py-3">Utilisasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($dosenWorkloads as $row)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $row['name'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['nidn'] ?: '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['total_mahasiswa'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['total_relasi'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['total_sesi'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['total_hadir'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['kuota'] }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="h-2.5 w-24 rounded-full bg-slate-100">
                                        <div class="h-2.5 rounded-full bg-linear-to-r from-blue-500 to-cyan-500"
                                            style="width: {{ min($row['persentase_beban'], 100) }}%"></div>
                                    </div>
                                    <span
                                        class="text-xs font-semibold text-slate-600">{{ $row['persentase_beban'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-500">Belum ada data dosen untuk
                                direkap.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-[0.95fr_1.05fr]">
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Status Penyelesaian TA</h2>
            <p class="mt-1 text-sm text-slate-500">Distribusi fase pengerjaan TA berdasarkan progres terbaru tiap
                mahasiswa.</p>

            <div class="mt-6 space-y-4">
                @forelse ($completionSummary['phase_distribution'] as $phase)
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <p class="font-medium text-slate-700">{{ $phase['phase'] }}</p>
                            <p class="text-slate-500">{{ $phase['count'] }} mahasiswa ({{ $phase['percentage'] }}%)
                            </p>
                        </div>
                        <div class="h-3 rounded-full bg-slate-100">
                            <div class="h-3 rounded-full bg-linear-to-r from-emerald-500 to-teal-500"
                                style="width: {{ max($phase['percentage'], 5) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p
                        class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                        Belum ada data status penyelesaian TA.
                    </p>
                @endforelse
            </div>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Detail Progres Mahasiswa TA</h2>
                    <p class="mt-1 text-sm text-slate-500">Digunakan untuk melihat capaian per mahasiswa dan menentukan
                        tindak
                        lanjut.</p>
                </div>
                <div>
                    <x-ui.show-entries wire:model.live="detailPerPage" class="focus:border-blue-500 focus:ring-blue-500"
                        label="Show entries" />
                </div>
            </div>

            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                        <tr>
                            <th class="px-4 py-3">Mahasiswa</th>
                            <th class="px-4 py-3">NIM</th>
                            <th class="px-4 py-3">Fase</th>
                            <th class="px-4 py-3">Progres</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($progressRows as $row)
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $row['name'] }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $row['nim'] }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $row['phase'] }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $row['progress'] }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-500">Belum ada data progres
                                    mahasiswa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $progressRows->links('vendor.pagination.tailwind') }}
            </div>
        </article>
    </section>
</div>
