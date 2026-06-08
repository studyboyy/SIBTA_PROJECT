<div class="space-y-6">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-blue-900 to-cyan-800 px-6 py-7 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-100/80">Portal Mahasiswa</p>
                <h1 class="mt-2 text-2xl font-semibold sm:text-3xl">Timeline Progres TA</h1>
                <p class="mt-2 max-w-2xl text-sm text-cyan-100 sm:text-base">
                    Lihat posisi tugas akhir Anda dari pengajuan judul sampai jadwal sidang.
                </p>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-cyan-100/70">Status TA</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $mahasiswa->status_ta }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-cyan-100/70">Progres</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['progress'] }}%</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-cyan-100/70">Bimbingan</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $bimbinganCount }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-[0.85fr_1.15fr]">
        <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-lg font-semibold text-slate-900">Ringkasan Saat Ini</h2>
            <div class="mt-4 space-y-3 text-sm">
                <div class="rounded-2xl border border-slate-200 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Fase</p>
                    <p class="mt-1 font-semibold text-slate-900">{{ $summary['phase'] }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Judul Terakhir</p>
                    <p class="mt-1 font-semibold text-slate-900">{{ $latestJudul?->judul ?? 'Belum ada pengajuan judul' }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $latestJudul?->status ? ucfirst($latestJudul->status) : '-' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pembimbing Aktif</p>
                    <p class="mt-1 font-semibold text-slate-900">{{ $pembimbingAktif?->dosen?->user?->name ?? 'Belum ditetapkan' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pengajuan Sidang</p>
                    <p class="mt-1 font-semibold text-slate-900">
                        {{ $pengajuanSidang ? ucfirst($pengajuanSidang->status_dosen ?? 'pending') : 'Belum diajukan' }}
                    </p>
                    @if ($pengajuanSidang?->catatan_dosen)
                        <p class="mt-1 text-xs text-slate-500">{{ $pengajuanSidang->catatan_dosen }}</p>
                    @endif
                </div>
            </div>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-lg font-semibold text-slate-900">Alur Tugas Akhir</h2>
            <div class="mt-5 space-y-3">
                @foreach ($timelineSteps as $index => $step)
                    <div class="flex gap-3 rounded-2xl border border-slate-200 px-4 py-3">
                        <div @class([
                            'flex size-9 shrink-0 items-center justify-center rounded-full text-sm font-semibold',
                            'bg-emerald-100 text-emerald-700' => $step['status'] === 'done',
                            'bg-blue-100 text-blue-700' => $step['status'] === 'active',
                            'bg-slate-100 text-slate-500' => $step['status'] === 'pending',
                        ])>
                            {{ $index + 1 }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="font-semibold text-slate-900">{{ $step['label'] }}</p>
                                <span @class([
                                    'rounded-full px-2.5 py-1 text-xs font-semibold',
                                    'bg-emerald-100 text-emerald-700' => $step['status'] === 'done',
                                    'bg-blue-100 text-blue-700' => $step['status'] === 'active',
                                    'bg-slate-100 text-slate-500' => $step['status'] === 'pending',
                                ])>
                                    {{ $step['status'] === 'done' ? 'Selesai' : ($step['status'] === 'active' ? 'Sedang berjalan' : 'Menunggu') }}
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-slate-600">{{ $step['description'] }}</p>
                            <p class="mt-1 text-xs font-medium text-slate-400">{{ $step['meta'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </article>
    </section>
</div>
