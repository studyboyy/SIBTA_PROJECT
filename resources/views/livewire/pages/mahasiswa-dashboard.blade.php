<div class="space-y-8">

    {{-- Hero Banner --}}
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-cyan-900 to-blue-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="space-y-2">
                <p class="text-sm font-medium uppercase tracking-[0.3em] text-cyan-100/80">Portal Mahasiswa</p>
                <h1 class="text-3xl font-semibold sm:text-4xl">Selamat datang, {{ Auth::user()->name }}</h1>
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

    {{-- Info bar akses --}}
    @if (!$hasPembimbing)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            <span class="font-semibold">Menu terkunci:</span>
            Bimbingan, dokumen, dan pengajuan sidang baru bisa diakses setelah dosen pembimbing ditetapkan.
            <a href="{{ route('mahasiswa.pengajuan-judul') }}" wire:navigate
                class="ml-1 font-semibold text-amber-900 underline underline-offset-2 hover:text-amber-700">
                Ajukan judul sekarang →
            </a>
        </div>
    @endif

    {{-- Stat Cards --}}
    <section class="grid grid-cols-2 gap-4 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">NIM</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $mahasiswa->nim }}</p>
            <p class="mt-1 text-xs text-slate-400">{{ $mahasiswa->prodi }} • {{ $mahasiswa->angkatan }}</p>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Dosen Pembimbing</p>
            @if ($primaryPembimbingName)
                <p class="mt-2 text-sm font-semibold text-slate-900 leading-snug">{{ $primaryPembimbingName }}</p>
                <p class="mt-1 text-xs text-slate-400">Pembimbing resmi Anda</p>
            @else
                <p class="mt-2 text-sm font-semibold text-amber-700">Belum ditetapkan</p>
                <a href="{{ route('mahasiswa.pengajuan-judul') }}" wire:navigate
                    class="mt-1 inline-block text-xs font-semibold text-blue-600 hover:underline">
                    Ajukan judul →
                </a>
            @endif
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Progress TA</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $summary['progress'] }}%</p>
            <div class="mt-3 h-2 rounded-full bg-slate-100">
                <div class="h-2 rounded-full bg-linear-to-r from-blue-600 to-cyan-500"
                    style="width: {{ max($summary['progress'], 4) }}%"></div>
            </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Dokumen Disetujui</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">
                {{ $summary['dokumen_approved'] }}/{{ $summary['dokumen_total'] }}
            </p>
            <p class="mt-1 text-xs text-slate-400">
                {{ $summary['dokumen_pending'] }} menunggu review
            </p>
        </article>
    </section>

    {{-- Stat Bimbingan --}}
    <section class="grid grid-cols-2 gap-4 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Sesi</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $summary['bimbingan_total'] }}</p>
            <p class="mt-1 text-xs text-slate-400">sesi bimbingan</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Sesi Hadir</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $summary['bimbingan_hadir'] }}</p>
            <p class="mt-1 text-xs text-slate-400">terkonfirmasi hadir</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Sesi Selesai</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-600">{{ $summary['sesi_selesai'] }}</p>
            <p class="mt-1 text-xs text-slate-400">berhasil diselesaikan</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Sesi Dibatalkan</p>
            <p class="mt-2 text-2xl font-semibold text-red-500">{{ $summary['sesi_dibatalkan'] }}</p>
            <p class="mt-1 text-xs text-slate-400">tidak terlaksana</p>
        </article>
    </section>

    {{-- Dokumen & Info Sidang --}}
    <section class="grid grid-cols-1 gap-6 2xl:grid-cols-2">
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-slate-900">Dokumen Terbaru</h2>
                <div class="flex items-center gap-3">
                    <a href="{{ route('mahasiswa.pengajuan-judul') }}" wire:navigate
                        class="text-xs font-semibold text-blue-600 hover:underline">Pengajuan judul</a>
                    <a href="{{ route('mahasiswa.dokumen') }}" wire:navigate
                        class="text-xs font-semibold text-blue-600 hover:underline">Kelola dokumen</a>
                </div>
            </div>
            <div class="mt-4 space-y-2">
                @forelse ($latestDokumen as $dokumen)
                    <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $dokumen->bab }}</p>
                            <p class="mt-0.5 text-xs text-slate-400">
                                {{ $dokumen->created_at?->translatedFormat('d M Y H:i') }}</p>
                        </div>
                        <span
                            class="rounded-full px-2.5 py-1 text-xs font-semibold {{ in_array(strtolower($dokumen->status), ['approved', 'disetujui']) ? 'bg-emerald-100 text-emerald-700' : (in_array(strtolower($dokumen->status), ['rejected', 'ditolak']) ? 'bg-red-100 text-red-700' : (strtolower($dokumen->status) === 'revisi' ? 'bg-orange-100 text-orange-700' : 'bg-amber-100 text-amber-700')) }}">
                            {{ ucfirst($dokumen->status) }}
                        </span>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-400">
                        Belum ada dokumen yang diunggah.
                    </div>
                @endforelse
            </div>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-slate-900">Info Sidang & Bimbingan</h2>
                <a href="{{ route('mahasiswa.bimbingan') }}" wire:navigate
                    class="text-xs font-semibold text-blue-600 hover:underline">Lihat bimbingan</a>
            </div>

            {{-- Status Sidang --}}
            <div class="mt-4 rounded-2xl border border-slate-200 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Status Sidang</p>
                @if ($sidang)
                    <div class="mt-2 space-y-1">
                        <p class="text-sm font-medium text-slate-800">
                            {{ \Carbon\Carbon::parse($sidang->jadwal)->translatedFormat('d M Y') }}
                        </p>
                        <p class="text-xs text-slate-500">Ruangan: {{ $sidang->ruangan ?? '-' }}</p>
                        <span class="inline-block rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">
                            {{ ucfirst($sidang->status ?? 'Terjadwal') }}
                        </span>
                    </div>
                @else
                    <p class="mt-2 text-sm text-slate-500">Belum ada jadwal sidang.</p>
                    <a href="{{ route('mahasiswa.pengajuan-sidang') }}" wire:navigate
                        class="mt-1 inline-block text-xs font-semibold text-blue-600 hover:underline">
                        Ajukan sidang →
                    </a>
                @endif
            </div>

            {{-- Jadwal Konsultasi Mendatang --}}
            <div class="mt-3 rounded-2xl border border-slate-200 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Konsultasi Mendatang</p>
                <div class="mt-2 space-y-2">
                    @forelse ($upcomingKonsultasi as $item)
                        <div class="flex items-center gap-3 rounded-xl bg-slate-50 px-3 py-2 text-xs text-slate-600">
                            <span class="font-medium text-slate-800">
                                {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}
                            </span>
                            <span class="text-slate-400">•</span>
                            <span>{{ $item->dosen?->user?->name ?? 'Dosen Pembimbing' }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">Tidak ada jadwal mendatang.</p>
                    @endforelse
                </div>
            </div>

            {{-- Riwayat Bimbingan --}}
            <div class="mt-3 space-y-2">
                @forelse ($latestBimbingan->take(3) as $bimbingan)
                    <div class="rounded-xl border border-slate-200 px-4 py-3">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-slate-800">
                                {{ $bimbingan->dosen?->user?->name ?? 'Dosen Pembimbing' }}
                            </p>
                            <p class="text-xs text-slate-400">
                                {{ $bimbingan->tanggal ? \Carbon\Carbon::parse($bimbingan->tanggal)->translatedFormat('d M Y') : '-' }}
                            </p>
                        </div>
                        @if ($bimbingan->catatan)
                            <p class="mt-1 text-xs text-slate-500 line-clamp-2">{{ $bimbingan->catatan }}</p>
                        @endif
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-6 text-center text-sm text-slate-400">
                        Belum ada riwayat bimbingan.
                    </div>
                @endforelse
            </div>
        </article>
    </section>
</div>
