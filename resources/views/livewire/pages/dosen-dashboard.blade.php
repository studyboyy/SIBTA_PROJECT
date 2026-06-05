<div class="space-y-8">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-indigo-900 to-cyan-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <p class="text-sm font-medium uppercase tracking-[0.3em] text-cyan-100/80">Panel Dosen SIBTA</p>
                <h1 class="text-3xl font-semibold sm:text-4xl">Halo, {{ Auth::user()->name }}</h1>
                <p class="max-w-2xl text-sm text-cyan-100 sm:text-base">
                    Kelola pengajuan judul mahasiswa bimbingan Anda dan pantau status persetujuan dari satu dashboard.
                </p>
            </div>
            <a href="{{ route('dosen.pengajuan-judul') }}" wire:navigate
                class="inline-flex items-center justify-center rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold text-white hover:bg-white/20">
                Review Pengajuan Judul
            </a>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Mahasiswa Bimbingan</p>
            <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $summary['totalMahasiswaBimbingan'] }}</p>
            <p class="mt-2 text-xs text-slate-500">Total mahasiswa yang dibimbing saat ini</p>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Pengajuan Menunggu</p>
            <p class="mt-2 text-3xl font-semibold text-amber-600">{{ $summary['pending'] }}</p>
            <p class="mt-2 text-xs text-slate-500">Perlu review dari dosen</p>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Pengajuan Disetujui</p>
            <p class="mt-2 text-3xl font-semibold text-emerald-600">{{ $summary['approved'] }}</p>
            <p class="mt-2 text-xs text-slate-500">Judul yang sudah approved</p>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Perlu Revisi / Ditolak</p>
            <p class="mt-2 text-3xl font-semibold text-rose-600">{{ $summary['revisi'] + $summary['rejected'] }}</p>
            <p class="mt-2 text-xs text-slate-500">Butuh tindak lanjut mahasiswa</p>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-4 xl:grid-cols-5">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Sesi Diajukan</p>
            <p class="mt-2 text-3xl font-semibold text-amber-600">{{ $summary['bimbinganDiajukan'] }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Sesi Disetujui</p>
            <p class="mt-2 text-3xl font-semibold text-blue-600">{{ $summary['bimbinganDisetujui'] }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Sesi Selesai</p>
            <p class="mt-2 text-3xl font-semibold text-emerald-600">{{ $summary['bimbinganSelesai'] }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Sesi Dibatalkan</p>
            <p class="mt-2 text-3xl font-semibold text-red-600">{{ $summary['bimbinganDibatalkan'] }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Progress Kehadiran</p>
            <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $summary['progressBimbingan'] }}%</p>
            <div class="mt-3 h-2 rounded-full bg-slate-100">
                <div class="h-2 rounded-full bg-linear-to-r from-indigo-600 to-cyan-500"
                    style="width: {{ max($summary['progressBimbingan'], 5) }}%"></div>
            </div>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-6 2xl:grid-cols-[1.2fr_0.8fr]">
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Pengajuan Judul Terbaru</h2>
                    <p class="text-sm text-slate-500">Daftar pengajuan terbaru dari mahasiswa bimbingan Anda.</p>
                </div>
                <a href="{{ route('dosen.pengajuan-judul') }}" wire:navigate
                    class="text-sm font-semibold text-blue-600">
                    Buka halaman review
                </a>
            </div>

            <div class="mt-6 space-y-3">
                @forelse ($latestPengajuan as $pengajuan)
                    @php
                        $status = strtolower($pengajuan->status ?? 'pending');
                        $statusClass = match ($status) {
                            'approved', 'disetujui' => 'bg-emerald-100 text-emerald-700',
                            'rejected', 'ditolak' => 'bg-red-100 text-red-700',
                            'revisi' => 'bg-orange-100 text-orange-700',
                            default => 'bg-amber-100 text-amber-700',
                        };
                    @endphp
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $pengajuan->judul }}</p>
                                <p class="text-sm text-slate-500">
                                    {{ $pengajuan->mahasiswa?->user?->name ?? 'Mahasiswa' }}
                                    • {{ $pengajuan->mahasiswa?->nim ?? '-' }}
                                </p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                {{ ucfirst($pengajuan->status ?? 'pending') }}
                            </span>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">
                            {{ $pengajuan->created_at?->translatedFormat('d M Y H:i') }}
                        </p>
                    </div>
                @empty
                    <div
                        class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                        Belum ada pengajuan judul dari mahasiswa bimbingan.
                    </div>
                @endforelse
            </div>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Ringkasan Cepat</h2>
            <p class="mt-1 text-sm text-slate-500">Statistik review pengajuan judul Anda.</p>

            <div class="mt-6 space-y-3 text-sm">
                <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2">
                    <span class="text-slate-600">Total pengajuan</span>
                    <span class="font-semibold text-slate-900">{{ $summary['totalPengajuan'] }}</span>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-amber-50 px-3 py-2">
                    <span class="text-amber-700">Pending</span>
                    <span class="font-semibold text-amber-800">{{ $summary['pending'] }}</span>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-emerald-50 px-3 py-2">
                    <span class="text-emerald-700">Approved</span>
                    <span class="font-semibold text-emerald-800">{{ $summary['approved'] }}</span>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-orange-50 px-3 py-2">
                    <span class="text-orange-700">Revisi</span>
                    <span class="font-semibold text-orange-800">{{ $summary['revisi'] }}</span>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-rose-50 px-3 py-2">
                    <span class="text-rose-700">Rejected</span>
                    <span class="font-semibold text-rose-800">{{ $summary['rejected'] }}</span>
                </div>
            </div>

            <div class="mt-6">
                <div class="space-y-2">
                    <a href="{{ route('dosen.pengajuan-judul') }}" wire:navigate
                        class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Buka Review Pengajuan
                    </a>
                    <a href="{{ route('dosen.kontrol-bimbingan') }}" wire:navigate
                        class="inline-flex w-full items-center justify-center rounded-xl bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-900">
                        Buka Kontrol Bimbingan
                    </a>
                </div>
            </div>
        </article>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Status Progress TA per Mahasiswa</h2>
                <p class="text-sm text-slate-500">Skor gabungan dari dokumen, kehadiran bimbingan, dan kesiapan sidang.
                </p>
            </div>
            <a href="{{ route('dosen.kontrol-bimbingan') }}" wire:navigate class="text-sm font-semibold text-blue-600">
                Buka kontrol bimbingan
            </a>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-2">
            @forelse ($progressMahasiswa as $item)
                @php
                    $priorityClass = match ($item['priority']) {
                        'aman' => 'bg-emerald-100 text-emerald-700',
                        'perlu perhatian' => 'bg-amber-100 text-amber-700',
                        default => 'bg-rose-100 text-rose-700',
                    };

                    $barClass = match ($item['priority']) {
                        'aman' => 'from-emerald-500 to-teal-500',
                        'perlu perhatian' => 'from-amber-500 to-orange-500',
                        default => 'from-rose-500 to-red-500',
                    };
                @endphp
                <article class="rounded-2xl border border-slate-200 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $item['name'] }}</p>
                            <p class="text-xs text-slate-500">{{ $item['nim'] }} • {{ $item['prodi'] }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $priorityClass }}">
                                {{ ucfirst($item['priority']) }}
                            </span>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                {{ $item['progress'] }}%
                            </span>
                        </div>
                    </div>

                    <div class="mt-3 h-2.5 rounded-full bg-slate-100">
                        <div class="h-2.5 rounded-full bg-linear-to-r {{ $barClass }}"
                            style="width: {{ max($item['progress'], 4) }}%"></div>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-2 text-[11px] text-slate-600">
                        <span class="rounded-full bg-slate-100 px-2.5 py-1">
                            Dok. wajib: {{ $item['approved_dokumen'] }}/{{ $item['total_dokumen'] }} disetujui
                        </span>
                        <span class="rounded-full bg-slate-100 px-2.5 py-1">
                            Hadir: {{ $item['hadir_bimbingan'] }}/{{ $item['target_bimbingan'] }} sesi target
                        </span>
                        <span class="rounded-full bg-slate-100 px-2.5 py-1">
                            Sidang: {{ ucfirst($item['sidang_status']) }}
                        </span>
                    </div>
                </article>
            @empty
                <div
                    class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500 xl:col-span-2">
                    Belum ada mahasiswa bimbingan untuk dihitung progresnya.
                </div>
            @endforelse
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Jadwal Bimbingan</h2>
                <p class="text-sm text-slate-500">Daftar jadwal bimbingan yang akan datang.</p>
            </div>
            <a href="{{ route('dosen.bimbingan') }}" wire:navigate class="text-sm font-semibold text-blue-600">Kelola
                bimbingan</a>
        </div>

        <div class="mt-5 space-y-3">
            @forelse ($jadwalBimbingan as $item)
                <div class="rounded-2xl border border-slate-200 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-900">
                                {{ $item->mahasiswa?->user?->name ?? 'Mahasiswa' }}
                            </p>
                            <p class="text-xs text-slate-500">{{ $item->mahasiswa?->nim ?? '-' }}</p>
                        </div>
                        <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">
                            {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') : '-' }}
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-slate-600">{{ $item->catatan ?: 'Belum ada catatan sesi.' }}</p>
                    <div class="mt-2 flex flex-wrap gap-2 text-xs">
                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-slate-700">
                            {{ ucfirst($item->mode ?? 'offline') }}
                        </span>
                        <span
                            class="rounded-full px-2.5 py-1 {{ ($item->status_sesi ?? 'diajukan') === 'selesai' ? 'bg-emerald-100 text-emerald-700' : (($item->status_sesi ?? 'diajukan') === 'disetujui' ? 'bg-blue-100 text-blue-700' : (($item->status_sesi ?? 'diajukan') === 'dibatalkan' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700')) }}">
                            {{ ucfirst($item->status_sesi ?? 'diajukan') }}
                        </span>
                    </div>
                </div>
            @empty
                <div
                    class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                    Belum ada jadwal bimbingan mendatang.
                </div>
            @endforelse
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 2xl:grid-cols-[1fr_1fr]">
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Status Judul per Mahasiswa</h2>
                    <p class="text-sm text-slate-500">Ringkasan judul terbaru untuk setiap mahasiswa bimbingan.</p>
                </div>
                <a href="{{ route('dosen.pengajuan-judul') }}" wire:navigate
                    class="text-sm font-semibold text-blue-600">
                    Kelola review
                </a>
            </div>

            <div class="mt-6 space-y-3">
                @forelse ($mahasiswaOverview as $item)
                    @php
                        $status = strtolower($item['latest_status'] ?? 'belum ada');
                        $statusClass = match ($status) {
                            'approved', 'disetujui' => 'bg-emerald-100 text-emerald-700',
                            'rejected', 'ditolak' => 'bg-red-100 text-red-700',
                            'revisi' => 'bg-orange-100 text-orange-700',
                            'pending' => 'bg-amber-100 text-amber-700',
                            default => 'bg-slate-100 text-slate-700',
                        };
                        $statusLabel = $item['latest_status'] ? ucfirst($item['latest_status']) : 'Belum mengajukan';
                    @endphp
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $item['name'] }}</p>
                                <p class="text-xs text-slate-500">{{ $item['nim'] }} •
                                    {{ $item['total_pengajuan'] }}
                                    pengajuan</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        <p class="mt-2 text-sm text-slate-600">
                            {{ $item['latest_judul'] ?: 'Belum ada judul yang diajukan.' }}
                        </p>

                        @if ($item['latest_updated_at'])
                            <p class="mt-1 text-xs text-slate-500">
                                Update terakhir: {{ $item['latest_updated_at']->translatedFormat('d M Y H:i') }}
                            </p>
                        @endif
                    </div>
                @empty
                    <div
                        class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                        Belum ada mahasiswa bimbingan.
                    </div>
                @endforelse
            </div>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Perlu Aksi Dosen</h2>
                    <p class="text-sm text-slate-500">Prioritas pengajuan dengan status pending atau revisi.</p>
                </div>
                <a href="{{ route('dosen.pengajuan-judul') }}" wire:navigate
                    class="text-sm font-semibold text-blue-600">
                    Buka semua
                </a>
            </div>

            <div class="mt-6 space-y-3">
                @forelse ($perluAksi as $pengajuan)
                    @php
                        $status = strtolower($pengajuan->status ?? 'pending');
                        $statusClass =
                            $status === 'revisi' ? 'bg-orange-100 text-orange-700' : 'bg-amber-100 text-amber-700';
                        $labelAksi = $status === 'revisi' ? 'Perlu review ulang' : 'Menunggu review';
                    @endphp
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $pengajuan->judul }}</p>
                                <p class="text-sm text-slate-500">
                                    {{ $pengajuan->mahasiswa?->user?->name ?? 'Mahasiswa' }}
                                    • {{ $pengajuan->mahasiswa?->nim ?? '-' }}
                                </p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                {{ ucfirst($pengajuan->status ?? 'pending') }}
                            </span>
                        </div>
                        <p class="mt-2 text-xs font-medium text-slate-600">{{ $labelAksi }}</p>
                        <p class="mt-1 text-xs text-slate-500">
                            Update: {{ $pengajuan->updated_at?->translatedFormat('d M Y H:i') }}
                        </p>
                    </div>
                @empty
                    <div
                        class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                        Tidak ada pengajuan yang perlu aksi saat ini.
                    </div>
                @endforelse
            </div>
        </article>
    </section>
</div>
