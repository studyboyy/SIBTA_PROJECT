<div class="space-y-8">

    {{-- Hero --}}
    <section class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-blue-900 to-cyan-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-2">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-100/80">Panel Admin SIBTA</p>
                <h1 class="text-3xl font-semibold sm:text-4xl">{{ $greeting }}, {{ Auth::user()->name }}</h1>
                <p class="max-w-2xl text-sm text-blue-100 sm:text-base">
                    Ringkasan progres tugas akhir — bimbingan aktif, kesiapan sidang, dan prioritas tindakan.
                </p>
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

    {{-- Quick stats row --}}
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="group relative overflow-hidden rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Total Dosen</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $summary['totalDosen'] }}</p>
                    <p class="mt-1 text-xs text-slate-400">Pembimbing & penguji tersedia</p>
                </div>
                <div class="rounded-xl bg-blue-50 p-2.5">
                    <svg class="size-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 h-0.5 rounded-full bg-blue-100"></div>
        </article>

        <article class="group relative overflow-hidden rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Sidang Mendatang</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $summary['sidangTerjadwal'] }}</p>
                    <p class="mt-1 text-xs text-slate-400">Hari ini & berikutnya</p>
                </div>
                <div class="rounded-xl bg-emerald-50 p-2.5">
                    <svg class="size-5 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V8.25A2.25 2.25 0 0 1 5.25 6h13.5A2.25 2.25 0 0 1 21 8.25v10.5A2.25 2.25 0 0 1 18.75 21H5.25A2.25 2.25 0 0 1 3 18.75ZM3 10.5h18" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 h-0.5 rounded-full bg-emerald-100"></div>
        </article>

        <article class="group relative overflow-hidden rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Perlu Tindakan</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ count($urgentMahasiswa) }}</p>
                    <p class="mt-1 text-xs text-slate-400">Mahasiswa prioritas</p>
                </div>
                <div class="rounded-xl bg-amber-50 p-2.5">
                    <svg class="size-5 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.644c-1.73 0-2.813-1.874-1.948-3.374L10.052 3.25c.866-1.5 3.03-1.5 3.896 0l7.355 12.876ZM12 16.5h.008v.008H12V16.5Z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 h-0.5 rounded-full bg-amber-100"></div>
        </article>

        <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Aksi Cepat</p>
            <div class="mt-3 grid grid-cols-1 gap-2">
                <a href="{{ route('bimbingan') }}" wire:navigate
                    class="flex items-center gap-2 rounded-xl bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-blue-50 hover:text-blue-700">
                    <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>
                    Kelola Bimbingan
                </a>
                <a href="{{ route('jadwal-sidang') }}" wire:navigate
                    class="flex items-center gap-2 rounded-xl bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-700">
                    <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V8.25A2.25 2.25 0 0 1 5.25 6h13.5A2.25 2.25 0 0 1 21 8.25v10.5A2.25 2.25 0 0 1 18.75 21H5.25A2.25 2.25 0 0 1 3 18.75ZM3 10.5h18" /></svg>
                    Jadwal Sidang
                </a>
                <a href="{{ route('pengelolaan-dokumen') }}" wire:navigate
                    class="flex items-center gap-2 rounded-xl bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-amber-50 hover:text-amber-700">
                    <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5A3.375 3.375 0 0010.125 2.25H6.75A2.25 2.25 0 004.5 4.5v15A2.25 2.25 0 006.75 21.75h10.5a2.25 2.25 0 002.25-2.25V14.25Z" /></svg>
                    Dokumen TA
                </a>
            </div>
        </article>
    </section>

    {{-- Content grid --}}
    <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">

        {{-- Progress Distribution --}}
        <article class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Distribusi Progres Mahasiswa</h2>
                    <p class="mt-0.5 text-xs text-slate-400">Berdasarkan dokumen, bimbingan, dan sidang</p>
                </div>
                <a href="{{ route('mahasiswa') }}" wire:navigate
                    class="text-xs font-semibold text-blue-600 hover:text-blue-700">Lihat semua →</a>
            </div>

            <div class="mt-5 space-y-3.5">
                @forelse ($statusDistribution as $status)
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <p class="text-sm font-medium text-slate-700">{{ $status['label'] }}</p>
                            <span class="text-xs font-semibold text-slate-500">{{ $status['count'] }} <span class="font-normal text-slate-400">mhs</span></span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100">
                            <div class="h-2 rounded-full bg-gradient-to-r from-blue-500 to-cyan-400 transition-all"
                                style="width: {{ max($status['percentage'], 3) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-200 py-8 text-center text-sm text-slate-400">
                        Belum ada data.
                    </div>
                @endforelse
            </div>
        </article>

        {{-- Siap Sidang --}}
        <article class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Siap Dijadwalkan Sidang</h2>
                    <p class="mt-0.5 text-xs text-slate-400">Dokumen lengkap, pembimbing ada, belum jadwal</p>
                </div>
                <a href="{{ route('jadwal-sidang') }}" wire:navigate
                    class="text-xs font-semibold text-blue-600 hover:text-blue-700">Atur sidang →</a>
            </div>

            <div class="mt-5 space-y-2.5">
                @forelse ($siapSidang as $item)
                    <div class="flex items-center justify-between rounded-xl border border-emerald-100 bg-emerald-50/50 px-4 py-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $item['name'] }}</p>
                            <p class="text-xs text-slate-500">{{ $item['nim'] }}</p>
                        </div>
                        <div class="ml-3 flex items-center gap-3">
                            <span class="text-xs font-bold text-emerald-600">{{ $item['progress'] }}%</span>
                            <a href="{{ route('mahasiswa.progres', ['mahasiswaId' => $item['id']]) }}" wire:navigate
                                class="text-slate-400 hover:text-emerald-600 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-200 py-8 text-center text-sm text-slate-400">
                        Belum ada mahasiswa siap sidang.
                    </div>
                @endforelse
            </div>
        </article>

        {{-- Butuh Perhatian --}}
        <article class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Mahasiswa Butuh Perhatian</h2>
                    <p class="mt-0.5 text-xs text-slate-400">Prioritas berdasarkan urgency score</p>
                </div>
                <a href="{{ route('pengelolaan-dokumen') }}" wire:navigate
                    class="text-xs font-semibold text-blue-600 hover:text-blue-700">Cek dokumen →</a>
            </div>

            <div class="mt-5 space-y-2.5">
                @forelse ($urgentMahasiswa as $item)
                    <div class="rounded-xl border border-slate-200 px-4 py-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="truncate text-sm font-semibold text-slate-900">{{ $item['name'] }}</p>
                                    <span class="shrink-0 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">
                                        {{ $item['progress'] }}%
                                    </span>
                                </div>
                                <p class="mt-0.5 text-xs text-slate-400">{{ $item['nim'] }} · {{ $item['phase'] }}</p>
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @foreach ($item['reasons'] as $reason)
                                        <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ $reason }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <a href="{{ route('mahasiswa.progres', ['mahasiswaId' => $item['id']]) }}" wire:navigate
                                class="shrink-0 text-slate-400 hover:text-blue-600 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-200 py-8 text-center text-sm text-slate-400">
                        Tidak ada mahasiswa prioritas saat ini.
                    </div>
                @endforelse
            </div>
        </article>

        {{-- Agenda Sidang --}}
        <article class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Agenda Sidang Terdekat</h2>
                    <p class="mt-0.5 text-xs text-slate-400">Jadwal sidang mendatang</p>
                </div>
                <a href="{{ route('jadwal-sidang') }}" wire:navigate
                    class="text-xs font-semibold text-blue-600 hover:text-blue-700">Kelola →</a>
            </div>

            <div class="mt-5 space-y-2.5">
                @forelse ($upcomingSidangs as $sidang)
                    <div class="rounded-xl border border-slate-200 px-4 py-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-900">
                                    {{ $sidang->mahasiswa?->user?->name ?? 'Mahasiswa' }}
                                </p>
                                <p class="mt-0.5 text-xs text-slate-400">
                                    {{ $sidang->ruangan ?? 'Ruangan belum ditentukan' }}
                                    · {{ $sidang->jam_mulai ? \Carbon\Carbon::parse($sidang->jam_mulai)->format('H:i') : '--:--' }}
                                </p>
                            </div>
                            <span class="shrink-0 rounded-xl bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                {{ \Carbon\Carbon::parse($sidang->jadwal)->translatedFormat('d M') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-200 py-8 text-center text-sm text-slate-400">
                        Belum ada agenda sidang mendatang.
                    </div>
                @endforelse
            </div>
        </article>
    </section>
</div>
