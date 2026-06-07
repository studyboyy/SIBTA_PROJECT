<div class="space-y-6">

    {{-- Hero --}}
    <section class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-cyan-900 to-blue-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="space-y-2">
                <p class="text-sm font-medium uppercase tracking-[0.3em] text-cyan-100/80">Portal Mahasiswa</p>
                <h1 class="text-3xl font-semibold sm:text-4xl">Halo, {{ Auth::user()->name }}</h1>
                <p class="max-w-2xl text-sm text-blue-100 sm:text-base">
                    Pantau progres skripsi, status dokumen, riwayat bimbingan, dan jadwal sidang dari satu halaman.
                </p>
            </div>
            @if ($hasPembimbing)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-1.5 text-xs font-semibold text-emerald-200 ring-1 ring-emerald-400/30">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                    Akses penuh aktif
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-400/20 px-3 py-1.5 text-xs font-semibold text-amber-200 ring-1 ring-amber-400/30">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                    Pembimbing belum ditetapkan
                </span>
            @endif
        </div>
    </section>

    {{-- Alert akses --}}
    @if (!$hasPembimbing)
        <div class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
            <svg class="mt-0.5 size-4 shrink-0 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            <p class="text-sm text-amber-800">
                <span class="font-semibold">Menu sebagian terkunci.</span>
                Bimbingan, dokumen, dan pengajuan sidang terbuka setelah pembimbing ditetapkan.
                <a href="{{ route('mahasiswa.pengajuan-judul') }}" wire:navigate
                    class="ml-1 font-semibold underline underline-offset-2">
                    Ajukan judul sekarang →
                </a>
            </p>
        </div>
    @endif

    {{-- Stat Grid --}}
    <section class="grid grid-cols-2 gap-4 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">NIM</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $mahasiswa->nim }}</p>
            <p class="mt-1 text-xs text-slate-400">{{ $mahasiswa->prodi }}</p>
        </article>

        <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Dosen Pembimbing</p>
            @if ($primaryPembimbingName)
                <p class="mt-2 text-sm font-bold text-slate-900 leading-snug">{{ $primaryPembimbingName }}</p>
                <p class="mt-1 text-xs text-emerald-600 font-medium">Sudah ditetapkan</p>
            @else
                <p class="mt-2 text-sm font-bold text-amber-600">Belum ditetapkan</p>
                <a href="{{ route('mahasiswa.pengajuan-judul') }}" wire:navigate
                    class="mt-1 inline-block text-xs font-semibold text-blue-600 hover:underline">
                    Ajukan judul →
                </a>
            @endif
        </article>

        <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Progress TA</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $summary['progress'] }}%</p>
            <div class="mt-2.5 h-1.5 rounded-full bg-slate-100">
                <div class="h-1.5 rounded-full bg-gradient-to-r from-blue-500 to-cyan-400 transition-all"
                    style="width: {{ max($summary['progress'], 3) }}%"></div>
            </div>
        </article>

        <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Dokumen Disetujui</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">
                {{ $summary['dokumen_approved'] }}<span class="text-lg text-slate-300">/{{ $summary['dokumen_total'] }}</span>
            </p>
            <p class="mt-1 text-xs text-slate-400">{{ $summary['dokumen_pending'] }} menunggu review</p>
        </article>
    </section>

    {{-- Bimbingan stats --}}
    <section class="grid grid-cols-2 gap-4 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium text-slate-400">Total Sesi</p>
            <p class="mt-1.5 text-xl font-bold text-slate-900">{{ $summary['bimbingan_total'] }}</p>
        </article>
        <article class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium text-slate-400">Sesi Hadir</p>
            <p class="mt-1.5 text-xl font-bold text-slate-900">{{ $summary['bimbingan_hadir'] }}</p>
        </article>
        <article class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium text-slate-400">Sesi Selesai</p>
            <p class="mt-1.5 text-xl font-bold text-emerald-600">{{ $summary['sesi_selesai'] }}</p>
        </article>
        <article class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium text-slate-400">Sesi Dibatalkan</p>
            <p class="mt-1.5 text-xl font-bold text-red-500">{{ $summary['sesi_dibatalkan'] }}</p>
        </article>
    </section>

    {{-- Dokumen & Info --}}
    <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">

        <article class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-900">Dokumen Terbaru</h2>
                <div class="flex gap-3">
                    <a href="{{ route('mahasiswa.pengajuan-judul') }}" wire:navigate
                        class="text-xs font-semibold text-blue-600 hover:underline">Pengajuan judul</a>
                    <a href="{{ route('mahasiswa.dokumen') }}" wire:navigate
                        class="text-xs font-semibold text-blue-600 hover:underline">Kelola</a>
                </div>
            </div>

            <div class="mt-4 space-y-2">
                @forelse ($latestDokumen as $dokumen)
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-2.5">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-slate-800">{{ $dokumen->bab }}</p>
                            <p class="text-xs text-slate-400">{{ $dokumen->created_at?->translatedFormat('d M Y') }}</p>
                        </div>
                        @php
                            $st = strtolower($dokumen->status ?? '');
                            $cls = in_array($st, ['approved','disetujui'])
                                ? 'bg-emerald-100 text-emerald-700'
                                : (in_array($st, ['rejected','ditolak']) ? 'bg-red-100 text-red-700'
                                : ($st === 'revisi' ? 'bg-orange-100 text-orange-700' : 'bg-amber-100 text-amber-700'));
                        @endphp
                        <span class="ml-3 shrink-0 rounded-full px-2 py-0.5 text-xs font-semibold {{ $cls }}">
                            {{ ucfirst($dokumen->status) }}
                        </span>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-200 py-6 text-center text-xs text-slate-400">
                        Belum ada dokumen yang diunggah.
                    </div>
                @endforelse
            </div>
        </article>

        <article class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-900">Info Sidang & Bimbingan</h2>
                <a href="{{ route('mahasiswa.bimbingan') }}" wire:navigate
                    class="text-xs font-semibold text-blue-600 hover:underline">Lihat semua</a>
            </div>

            {{-- Sidang --}}
            <div class="mt-4 rounded-xl bg-slate-50 px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Jadwal Sidang</p>
                @if ($sidang)
                    <p class="mt-1.5 text-sm font-semibold text-slate-900">
                        {{ \Carbon\Carbon::parse($sidang->jadwal)->translatedFormat('d M Y') }}
                    </p>
                    <div class="mt-1 flex items-center gap-3 text-xs text-slate-500">
                        <span>{{ $sidang->ruangan ?? '-' }}</span>
                        <span class="rounded-full bg-blue-100 px-2 py-0.5 font-semibold text-blue-700">
                            {{ ucfirst($sidang->status ?? 'Terjadwal') }}
                        </span>
                    </div>
                @else
                    <p class="mt-1.5 text-sm text-slate-400">Belum ada jadwal sidang.</p>
                    <a href="{{ route('mahasiswa.pengajuan-sidang') }}" wire:navigate
                        class="mt-1 inline-block text-xs font-semibold text-blue-600 hover:underline">
                        Ajukan sidang →
                    </a>
                @endif
            </div>

            {{-- Konsultasi mendatang --}}
            <div class="mt-3 rounded-xl bg-slate-50 px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Konsultasi Mendatang</p>
                <div class="mt-2 space-y-1.5">
                    @forelse ($upcomingKonsultasi as $item)
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-medium text-slate-700">
                                {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}
                            </span>
                            <span class="text-slate-400">{{ $item->dosen?->user?->name ?? 'Dosen' }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">Tidak ada jadwal mendatang.</p>
                    @endforelse
                </div>
            </div>

            {{-- Riwayat bimbingan --}}
            <div class="mt-3 space-y-2">
                @forelse ($latestBimbingan->take(3) as $bimbingan)
                    <div class="rounded-xl border border-slate-200 px-4 py-2.5">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold text-slate-700">
                                {{ $bimbingan->dosen?->user?->name ?? 'Dosen Pembimbing' }}
                            </p>
                            <p class="text-xs text-slate-400">
                                {{ $bimbingan->tanggal ? \Carbon\Carbon::parse($bimbingan->tanggal)->translatedFormat('d M') : '-' }}
                            </p>
                        </div>
                        @if ($bimbingan->catatan)
                            <p class="mt-0.5 line-clamp-1 text-xs text-slate-400">{{ $bimbingan->catatan }}</p>
                        @endif
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-200 py-5 text-center text-xs text-slate-400">
                        Belum ada riwayat bimbingan.
                    </div>
                @endforelse
            </div>
        </article>
    </section>
</div>
