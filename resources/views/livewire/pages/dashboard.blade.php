<div class="space-y-8">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-blue-900 to-cyan-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <p class="text-sm font-medium uppercase tracking-[0.3em] text-cyan-100/80">Panel Admin SIBTA</p>
                <div class="space-y-2">
                    <h1 class="text-3xl font-semibold sm:text-4xl">{{ $greeting }}, {{ Auth::user()->name }}</h1>
                    <p class="max-w-2xl text-sm text-blue-100 sm:text-base">
                        Ringkasan progres tugas akhir untuk memantau mahasiswa yang sedang bimbingan, siap sidang,
                        dan yang masih butuh tindakan admin.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-cyan-100/70">Mahasiswa TA</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['totalMahasiswa'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-cyan-100/70">Aktif Bimbingan</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['aktifBimbingan'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-cyan-100/70">Siap Sidang</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['siapSidang'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-cyan-100/70">Rata-rata Progres</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['rataRataProgres'] }}%</p>
                </div>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Total Dosen</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $summary['totalDosen'] }}</p>
                </div>
                <div class="rounded-2xl bg-blue-100 p-3 text-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </div>
            </div>
            <p class="mt-4 text-sm text-slate-500">Jumlah dosen yang tersedia untuk pembimbing dan penguji.</p>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Jadwal Sidang</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $summary['sidangTerjadwal'] }}</p>
                </div>
                <div class="rounded-2xl bg-emerald-100 p-3 text-emerald-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V8.25A2.25 2.25 0 0 1 5.25 6h13.5A2.25 2.25 0 0 1 21 8.25v10.5A2.25 2.25 0 0 1 18.75 21H5.25A2.25 2.25 0 0 1 3 18.75ZM3 10.5h18" />
                    </svg>
                </div>
            </div>
            <p class="mt-4 text-sm text-slate-500">Total sidang dengan tanggal hari ini atau setelahnya.</p>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Butuh Tindakan</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ count($urgentMahasiswa) }}</p>
                </div>
                <div class="rounded-2xl bg-amber-100 p-3 text-amber-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.644c-1.73 0-2.813-1.874-1.948-3.374L10.052 3.25c.866-1.5 3.03-1.5 3.896 0l7.355 12.876ZM12 16.5h.008v.008H12V16.5Z" />
                    </svg>
                </div>
            </div>
            <p class="mt-4 text-sm text-slate-500">Mahasiswa prioritas yang belum lengkap atau belum punya pembimbing.
            </p>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Aksi Cepat</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900">Kelola Modul</p>
                </div>
                <div class="rounded-2xl bg-slate-100 p-3 text-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.5 6h9.75M10.5 12h9.75M10.5 18h9.75M3.75 6h.008v.008H3.75V6Zm0 6h.008v.008H3.75V12Zm0 6h.008v.008H3.75V18Z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap gap-2 text-sm font-medium">
                <a href="{{ route('bimbingan') }}" wire:navigate
                    class="rounded-full bg-blue-50 px-3 py-2 text-blue-700">Bimbingan</a>
                <a href="{{ route('jadwal-sidang') }}" wire:navigate
                    class="rounded-full bg-emerald-50 px-3 py-2 text-emerald-700">Jadwal Sidang</a>
                <a href="{{ route('pengelolaan-dokumen') }}" wire:navigate
                    class="rounded-full bg-amber-50 px-3 py-2 text-amber-700">Dokumen TA</a>
            </div>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-6 2xl:grid-cols-[1.1fr_0.9fr]">
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Distribusi Progres Mahasiswa</h2>
                    <p class="text-sm text-slate-500">Fase tugas akhir berdasarkan aktivitas bimbingan, dokumen, dan
                        sidang.</p>
                </div>
                <a href="{{ route('mahasiswa') }}" wire:navigate class="text-sm font-semibold text-blue-600">Lihat
                    mahasiswa</a>
            </div>

            <div class="mt-6 space-y-4">
                @forelse ($statusDistribution as $status)
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <p class="font-medium text-slate-700">{{ $status['label'] }}</p>
                            <p class="text-slate-500">{{ $status['count'] }} mahasiswa</p>
                        </div>
                        <div class="h-3 rounded-full bg-slate-100">
                            <div class="h-3 rounded-full bg-linear-to-r from-blue-600 to-cyan-500"
                                style="width: {{ max($status['percentage'], 6) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div
                        class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                        Belum ada data progres mahasiswa.
                    </div>
                @endforelse
            </div>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Mahasiswa Siap Dijadwalkan</h2>
                    <p class="text-sm text-slate-500">Mahasiswa dengan dokumen lengkap, pembimbing sudah ada, dan belum
                        punya jadwal sidang.</p>
                </div>
                <a href="{{ route('jadwal-sidang') }}" wire:navigate class="text-sm font-semibold text-blue-600">Atur
                    sidang</a>
            </div>

            <div class="mt-6 space-y-3">
                @forelse ($siapSidang as $item)
                    <div class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $item['name'] }}</p>
                                <p class="text-sm text-slate-500">{{ $item['nim'] }} • {{ $item['prodi'] }}</p>
                            </div>
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                {{ $item['progress'] }}%
                            </span>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2 text-xs text-emerald-800">
                            <span
                                class="rounded-full bg-white px-3 py-1">{{ $item['approved_documents'] }}/{{ $item['documents_count'] }}
                                dokumen disetujui</span>
                            <span class="rounded-full bg-white px-3 py-1">{{ $item['bimbingan_count'] }} catatan
                                bimbingan</span>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('mahasiswa.progres', ['mahasiswaId' => $item['id']]) }}" wire:navigate
                                class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-900">
                                Lihat detail progres
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div
                        class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                        Belum ada mahasiswa yang siap dijadwalkan sidang.
                    </div>
                @endforelse
            </div>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-6 2xl:grid-cols-[1fr_1fr]">
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Mahasiswa Perlu Perhatian</h2>
                    <p class="text-sm text-slate-500">Prioritas tindakan admin berdasarkan pembimbing, dokumen, dan
                        kesiapan sidang.</p>
                </div>
                <a href="{{ route('pengelolaan-dokumen') }}" wire:navigate
                    class="text-sm font-semibold text-blue-600">Buka dokumen</a>
            </div>

            <div class="mt-6 space-y-4">
                @forelse ($urgentMahasiswa as $item)
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $item['name'] }}</p>
                                <p class="text-sm text-slate-500">{{ $item['nim'] }} • {{ $item['phase'] }}</p>
                            </div>
                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                {{ $item['progress'] }}%
                            </span>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($item['reasons'] as $reason)
                                <span
                                    class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">{{ $reason }}</span>
                            @endforeach
                        </div>
                        <div class="mt-3 text-xs text-slate-500">
                            @if ($item['supervisors']->isNotEmpty())
                                Pembimbing: {{ $item['supervisors']->join(', ') }}
                            @else
                                Pembimbing belum ditentukan
                            @endif
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('mahasiswa.progres', ['mahasiswaId' => $item['id']]) }}" wire:navigate
                                class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                                Lihat detail progres
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div
                        class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                        Tidak ada mahasiswa prioritas saat ini.
                    </div>
                @endforelse
            </div>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Agenda Sidang Terdekat</h2>
                    <p class="text-sm text-slate-500">Jadwal sidang mendatang beserta ketua dan penguji.</p>
                </div>
                <a href="{{ route('jadwal-sidang') }}" wire:navigate
                    class="text-sm font-semibold text-blue-600">Kelola jadwal</a>
            </div>

            <div class="mt-6 space-y-4">
                @forelse ($upcomingSidangs as $sidang)
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900">
                                    {{ $sidang->mahasiswa?->user?->name ?? 'Mahasiswa' }}</p>
                                <p class="text-sm text-slate-500">{{ $sidang->mahasiswa?->nim ?? '-' }} •
                                    {{ $sidang->ruangan ?? 'Ruangan belum ditentukan' }}</p>
                            </div>
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                {{ \Carbon\Carbon::parse($sidang->jadwal)->translatedFormat('d M Y') }}
                            </span>
                        </div>
                        <div class="mt-3 grid gap-2 text-sm text-slate-600 sm:grid-cols-2">
                            <p>Jam:
                                {{ $sidang->jam_mulai ? \Carbon\Carbon::parse($sidang->jam_mulai)->format('H:i') : '--:--' }}
                                -
                                {{ $sidang->jam_selesai ? \Carbon\Carbon::parse($sidang->jam_selesai)->format('H:i') : '--:--' }}
                            </p>
                            <p>Status: {{ ucfirst($sidang->status ?? 'pending') }}</p>
                            <p>Ketua: {{ $sidang->ketuaSidang?->user?->name ?? 'Belum ditentukan' }}</p>
                            <p>Penguji:
                                {{ collect([$sidang->penguji1?->user?->name, $sidang->penguji2?->user?->name])->filter()->join(', ') ?:'Belum lengkap' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div
                        class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                        Belum ada agenda sidang mendatang.
                    </div>
                @endforelse
            </div>
        </article>
    </section>
</div>
