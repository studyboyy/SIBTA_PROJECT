<div class="space-y-8">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-slate-800 to-blue-900 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-4">
                <a href="{{ route('dashboard') }}" wire:navigate
                    class="inline-flex items-center gap-2 text-sm font-medium text-blue-100/90 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Kembali ke dashboard
                </a>
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-blue-100/70">Detail Progres Mahasiswa</p>
                    <h1 class="mt-2 text-3xl font-semibold sm:text-4xl">{{ $mahasiswa->user?->name ?? 'Mahasiswa' }}
                    </h1>
                    <p class="mt-2 text-sm text-blue-100 sm:text-base">{{ $mahasiswa->nim }} • {{ $mahasiswa->prodi }} •
                        Angkatan {{ $mahasiswa->angkatan }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-blue-100/70">Progress</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['progress'] }}%</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-blue-100/70">Fase</p>
                    <p class="mt-2 text-base font-semibold">{{ $summary['phase'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-blue-100/70">Dokumen</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['documents_count'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-blue-100/70">Bimbingan</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['bimbingan_count'] }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Ringkasan Status</h2>
                    <p class="text-sm text-slate-500">Kondisi umum mahasiswa berdasarkan data yang sudah masuk ke
                        sistem.</p>
                </div>
                <span
                    class="rounded-full px-3 py-1 text-xs font-semibold {{ $summary['is_ready_for_sidang'] ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ $summary['is_ready_for_sidang'] ? 'Siap Sidang' : 'Perlu Tindakan' }}
                </span>
            </div>

            <div class="mt-6 space-y-5">
                <div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-medium text-slate-700">Progres keseluruhan</span>
                        <span class="text-slate-500">{{ $summary['progress'] }}%</span>
                    </div>
                    <div class="mt-2 h-3 rounded-full bg-slate-100">
                        <div class="h-3 rounded-full bg-linear-to-r from-blue-600 to-cyan-500"
                            style="width: {{ max($summary['progress'], 8) }}%"></div>
                    </div>
                </div>

                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-sm text-slate-500">Status TA</dt>
                        <dd class="mt-1 font-semibold text-slate-900">{{ $summary['status_ta'] }}</dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-sm text-slate-500">Pembimbing</dt>
                        <dd class="mt-1 font-semibold text-slate-900">
                            {{ $summary['supervisors']->isNotEmpty() ? $summary['supervisors']->join(', ') : 'Belum ditentukan' }}
                        </dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-sm text-slate-500">Dokumen disetujui</dt>
                        <dd class="mt-1 font-semibold text-slate-900">{{ $summary['approved_documents'] }} dari
                            {{ $summary['documents_count'] }}</dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-sm text-slate-500">Sidang</dt>
                        <dd class="mt-1 font-semibold text-slate-900">
                            {{ $summary['has_sidang'] ? 'Sudah terjadwal' : 'Belum terjadwal' }}</dd>
                    </div>
                </dl>

                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                    <h3 class="text-sm font-semibold text-amber-900">Catatan perhatian admin</h3>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @forelse ($summary['reasons'] as $reason)
                            <span
                                class="rounded-full bg-white px-3 py-1 text-xs font-medium text-amber-800">{{ $reason }}</span>
                        @empty
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-medium text-emerald-700">Tidak ada
                                kendala utama saat ini</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Aksi Lanjutan</h2>
                    <p class="text-sm text-slate-500">Masuk cepat ke modul yang relevan untuk mahasiswa ini.</p>
                </div>
            </div>

            <div class="mt-6 grid gap-3">
                <a href="{{ route('bimbingan') }}" wire:navigate
                    class="rounded-2xl border border-blue-200 bg-blue-50 px-4 py-4 text-sm font-semibold text-blue-700">
                    Kelola pembimbing dan catatan bimbingan
                </a>
                <a href="{{ route('pengelolaan-dokumen') }}" wire:navigate
                    class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 text-sm font-semibold text-amber-700">
                    Tinjau dokumen tugas akhir
                </a>
                <a href="{{ route('jadwal-sidang') }}" wire:navigate
                    class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm font-semibold text-emerald-700">
                    Atur jadwal sidang dan penguji
                </a>
                <a href="{{ route('mahasiswa') }}" wire:navigate
                    class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm font-semibold text-slate-700">
                    Kembali ke data mahasiswa
                </a>
            </div>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-[1fr_1fr]">
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Riwayat Dokumen</h2>
                    <p class="text-sm text-slate-500">Status dokumen yang sudah diunggah mahasiswa.</p>
                </div>
            </div>

            <div class="mt-6 space-y-3">
                @forelse ($dokumenList as $dokumen)
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $dokumen->bab ?: 'Dokumen Tugas Akhir' }}
                                </p>
                                <p class="text-sm text-slate-500">Diunggah
                                    {{ $dokumen->created_at?->translatedFormat('d M Y H:i') ?? '-' }}</p>
                            </div>
                            <span
                                class="rounded-full px-3 py-1 text-xs font-semibold {{ strtolower($dokumen->status) === 'approved' || strtolower($dokumen->status) === 'disetujui' ? 'bg-emerald-100 text-emerald-700' : (strtolower($dokumen->status) === 'rejected' || strtolower($dokumen->status) === 'ditolak' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                                {{ ucfirst($dokumen->status) }}
                            </span>
                        </div>
                        <p class="mt-3 text-sm text-slate-600">{{ $dokumen->file ?: 'File belum tersedia di data.' }}
                        </p>
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
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Riwayat Bimbingan dan Sidang</h2>
                    <p class="text-sm text-slate-500">Aktivitas pembimbingan terakhir dan status sidang mahasiswa.</p>
                </div>
            </div>

            <div class="mt-6 space-y-4">
                <div class="rounded-2xl border border-slate-200 p-4">
                    <h3 class="text-sm font-semibold text-slate-900">Status Sidang</h3>
                    @if ($sidang)
                        <div class="mt-3 grid gap-2 text-sm text-slate-600 sm:grid-cols-2">
                            <p>Tanggal:
                                {{ $sidang->jadwal ? \Carbon\Carbon::parse($sidang->jadwal)->translatedFormat('d M Y') : '-' }}
                            </p>
                            <p>Jam:
                                {{ $sidang->jam_mulai ? \Carbon\Carbon::parse($sidang->jam_mulai)->format('H:i') : '--:--' }}
                                -
                                {{ $sidang->jam_selesai ? \Carbon\Carbon::parse($sidang->jam_selesai)->format('H:i') : '--:--' }}
                            </p>
                            <p>Ruangan: {{ $sidang->ruangan ?? 'Belum ditentukan' }}</p>
                            <p>Status: {{ ucfirst($sidang->status ?? 'pending') }}</p>
                            <p>Ketua: {{ $sidang->ketuaSidang?->user?->name ?? 'Belum ditentukan' }}</p>
                            <p>Penguji:
                                {{ collect([$sidang->penguji1?->user?->name, $sidang->penguji2?->user?->name])->filter()->join(', ') ?:'Belum lengkap' }}
                            </p>
                        </div>
                    @else
                        <p class="mt-3 text-sm text-slate-500">Mahasiswa ini belum memiliki jadwal sidang.</p>
                    @endif
                </div>

                <div class="space-y-3">
                    @forelse ($bimbinganList as $bimbingan)
                        <div class="rounded-2xl border border-slate-200 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900">
                                        {{ $bimbingan->dosen?->user?->name ?? 'Dosen pembimbing' }}</p>
                                    <p class="text-sm text-slate-500">
                                        {{ $bimbingan->tanggal ? \Carbon\Carbon::parse($bimbingan->tanggal)->translatedFormat('d M Y') : '-' }}
                                    </p>
                                </div>
                                <span
                                    class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ ucfirst($bimbingan->status ?? 'pending') }}</span>
                            </div>
                            <p class="mt-3 text-sm text-slate-600">
                                {{ $bimbingan->catatan ?: 'Belum ada catatan bimbingan.' }}</p>
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
