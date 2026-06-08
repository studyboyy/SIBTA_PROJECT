<div class="space-y-6">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-emerald-900 to-cyan-800 px-6 py-7 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-emerald-100/80">Portal Mahasiswa</p>
                <h1 class="mt-2 text-2xl font-semibold sm:text-3xl">Checklist Kesiapan Sidang</h1>
                <p class="mt-2 max-w-2xl text-sm text-emerald-100 sm:text-base">
                    Pastikan seluruh syarat utama sudah terpenuhi sebelum mengirim atau menunggu pengajuan sidang.
                </p>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-emerald-100/70">Terpenuhi</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $readyCount }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-emerald-100/70">Total</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $totalCount }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-emerald-100/70">Status</p>
                    <p class="mt-2 text-lg font-semibold">{{ $isEligible ? 'Siap Ajukan' : 'Belum Siap' }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Syarat dan Tindak Lanjut</h2>
                <p class="text-sm text-slate-500">Buka halaman terkait untuk menyelesaikan item yang belum terpenuhi.</p>
            </div>
            @if ($isEligible)
                <a href="{{ route('mahasiswa.pengajuan-sidang') }}" wire:navigate
                    class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                    Ajukan Sidang
                </a>
            @endif
        </div>

        <div class="mt-5 grid gap-3 lg:grid-cols-2">
            @foreach ($items as $item)
                <div class="rounded-2xl border border-slate-200 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $item['label'] }}</p>
                            <p class="mt-1 text-sm text-slate-600">{{ $item['description'] }}</p>
                        </div>
                        <span @class([
                            'shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold',
                            'bg-emerald-100 text-emerald-700' => $item['done'],
                            'bg-amber-100 text-amber-700' => ! $item['done'],
                        ])>
                            {{ $item['done'] ? 'Terpenuhi' : 'Belum' }}
                        </span>
                    </div>
                    @if (! $item['done'])
                        <a href="{{ $item['action'] }}" wire:navigate
                            class="mt-3 inline-flex rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200">
                            {{ $item['action_label'] }}
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </section>
</div>
