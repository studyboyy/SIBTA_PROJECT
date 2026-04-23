<div class="space-y-8">
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status Akses Fitur</p>
                <p class="mt-1 text-sm text-slate-600">Akses halaman selain dashboard mengikuti status penentuan dosen
                    pembimbing.</p>
            </div>

            @if ($hasPembimbing)
                <span
                    class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                    Terbuka: pembimbing sudah ditentukan
                </span>
            @else
                <span
                    class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                    Terkunci: menunggu pembimbing
                </span>
            @endif
        </div>
    </section>

    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-cyan-900 to-blue-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-cyan-100/80">Portal Mahasiswa</p>
            <h1 class="text-3xl font-semibold sm:text-4xl">Selamat datang, {{ Auth::user()->name }}</h1>
            <p class="max-w-2xl text-sm text-blue-100 sm:text-base">
                Pantau progres tugas akhir, status dokumen, riwayat bimbingan, dan informasi sidang dari satu halaman.
            </p>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 xl:grid-cols-5">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">NIM</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $mahasiswa->nim }}</p>
            <p class="mt-2 text-xs text-slate-500">{{ $mahasiswa->prodi }} • {{ $mahasiswa->angkatan }}</p>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Dosen Pembimbing</p>
            @if ($primaryPembimbingName)
                <p class="mt-2 text-base font-semibold text-slate-900">{{ $primaryPembimbingName }}</p>
                <p class="mt-2 text-xs text-slate-500">Pembimbing terkait pengajuan judul Anda</p>
            @else
                <p class="mt-2 text-sm font-semibold text-amber-700">Belum ditentukan</p>
                <p class="mt-2 text-xs text-slate-500">Hubungi admin untuk penentuan pembimbing</p>
            @endif
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Progress TA</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $summary['progress'] }}%</p>
            <div class="mt-3 h-2 rounded-full bg-slate-100">
                <div class="h-2 rounded-full bg-linear-to-r from-blue-600 to-cyan-500"
                    style="width: {{ max($summary['progress'], 5) }}%"></div>
            </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Dokumen Disetujui</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">
                {{ $summary['dokumen_approved'] }}/{{ $summary['dokumen_total'] }}</p>
            <p class="mt-2 text-xs text-slate-500">{{ $summary['dokumen_pending'] }} dokumen menunggu review</p>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Bimbingan</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $summary['progress_bimbingan'] }}%</p>
            <p class="mt-2 text-xs text-slate-500">{{ $summary['bimbingan_hadir'] }}/{{ $summary['bimbingan_total'] }}
                sesi Anda hadiri</p>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Sesi Selesai</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-600">{{ $summary['sesi_selesai'] }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Sesi Dibatalkan</p>
            <p class="mt-2 text-2xl font-semibold text-red-600">{{ $summary['sesi_dibatalkan'] }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Progress Kehadiran</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $summary['progress_bimbingan'] }}%</p>
            <p class="mt-2 text-xs text-slate-500">{{ $summary['bimbingan_hadir'] }}/{{ $summary['bimbingan_total'] }}
                sesi hadir</p>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-6 2xl:grid-cols-[1fr_1fr]">
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Dokumen Terbaru</h2>
                <div class="flex items-center gap-4">
                    <a href="{{ route('mahasiswa.pengajuan-judul') }}" wire:navigate
                        class="text-sm font-semibold text-blue-600">Pengajuan judul</a>
                    <a href="{{ route('mahasiswa.dokumen') }}" wire:navigate
                        class="text-sm font-semibold text-blue-600">Kelola dokumen</a>
                </div>
            </div>
            <div class="mt-4 space-y-3">
                @forelse ($latestDokumen as $dokumen)
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-slate-900">{{ $dokumen->bab }}</p>
                            <span
                                class="rounded-full px-3 py-1 text-xs font-semibold {{ in_array(strtolower($dokumen->status), ['approved', 'disetujui']) ? 'bg-emerald-100 text-emerald-700' : (in_array(strtolower($dokumen->status), ['rejected', 'ditolak']) ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">{{ ucfirst($dokumen->status) }}</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ $dokumen->created_at?->translatedFormat('d M Y H:i') }}</p>
                    </div>
                @empty
                    <div
                        class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                        Belum ada dokumen yang diunggah.
                    </div>
                @endforelse
            </div>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Info Bimbingan & Sidang</h2>
                <a href="{{ route('mahasiswa.bimbingan') }}" wire:navigate
                    class="text-sm font-semibold text-blue-600">Buka bimbingan</a>
            </div>
            <div class="mt-4 space-y-4">
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-sm font-semibold text-slate-900">Status Sidang</p>
                    @if ($sidang)
                        <p class="mt-2 text-sm text-slate-600">Tanggal:
                            {{ \Carbon\Carbon::parse($sidang->jadwal)->translatedFormat('d M Y') }}</p>
                        <p class="text-sm text-slate-600">Ruangan: {{ $sidang->ruangan ?? 'Belum ditentukan' }}</p>
                        <p class="text-sm text-slate-600">Status: {{ ucfirst($sidang->status ?? 'pending') }}</p>
                    @else
                        <p class="mt-2 text-sm text-slate-500">Belum ada jadwal sidang.</p>
                    @endif
                </div>

                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-sm font-semibold text-slate-900">Jadwal Konsultasi</p>
                    <div class="mt-2 space-y-2">
                        @forelse ($upcomingKonsultasi as $item)
                            <div class="rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-600">
                                {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }} •
                                {{ $item->dosen?->user?->name ?? 'Dosen Pembimbing' }}
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Belum ada jadwal konsultasi mendatang.</p>
                        @endforelse
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse ($latestBimbingan as $bimbingan)
                        <div class="rounded-2xl border border-slate-200 p-4">
                            <p class="font-semibold text-slate-900">
                                {{ $bimbingan->dosen?->user?->name ?? 'Dosen Pembimbing' }}</p>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ $bimbingan->tanggal ? \Carbon\Carbon::parse($bimbingan->tanggal)->translatedFormat('d M Y') : '-' }}
                            </p>
                            <p class="mt-2 text-sm text-slate-600">{{ $bimbingan->catatan ?: 'Belum ada catatan.' }}
                            </p>
                        </div>
                    @empty
                        <div
                            class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                            Belum ada riwayat bimbingan.
                        </div>
                    @endforelse
                </div>
            </div>
        </article>
    </section>
</div>
