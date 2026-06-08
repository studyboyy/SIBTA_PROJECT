<div class="space-y-6">

    <section class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-cyan-900 to-teal-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="space-y-2">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-100/80">Panel Kaprodi</p>
                <h1 class="text-3xl font-semibold sm:text-4xl">Pengajuan Dosen Pembimbing</h1>
                <p class="max-w-2xl text-sm text-cyan-100 sm:text-base">
                    Tinjau pengajuan dosen pembimbing dari mahasiswa di prodi Anda. Approval judul skripsi tetap dilakukan oleh dosen pembimbing.
                </p>
            </div>
            @if ($managedProdi)
                <div class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-cyan-100/70">Prodi</p>
                    <p class="mt-1 text-lg font-semibold">{{ $managedProdi->name }}</p>
                </div>
            @endif
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Pengajuan Dosen dari Mahasiswa</h2>
                <p class="text-sm text-slate-500">Jika disetujui, dosen pembimbing mahasiswa akan otomatis diganti.</p>
            </div>
            <div class="flex gap-2">
                <input type="text" wire:model.live.debounce.300ms="searchPembimbing"
                    placeholder="Cari nama/NIM..."
                    class="w-40 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-100" />
                <select wire:model.live="statusPembimbingFilter"
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-100">
                    <option value="">Semua</option>
                    <option value="pending">Menunggu</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                </select>
            </div>
        </div>

        <div class="mt-5 space-y-3">
            @forelse ($pengajuanPembimbingList as $pp)
                @php
                    $ppColor = match ($pp->status) {
                        'approved' => 'bg-emerald-100 text-emerald-700',
                        'rejected' => 'bg-red-100 text-red-700',
                        default => 'bg-amber-100 text-amber-700',
                    };
                    $ppLabel = match ($pp->status) {
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => 'Menunggu',
                    };
                    $pembimbingLama = $pp->mahasiswa?->bimbingans?->first()?->dosen?->user?->name;
                @endphp
                <article class="rounded-2xl border border-slate-200 p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="font-semibold text-slate-900">
                                {{ $pp->mahasiswa?->user?->name ?? '-' }}
                                <span class="font-normal text-slate-500">- {{ $pp->mahasiswa?->nim ?? '-' }}</span>
                            </p>
                            <p class="mt-0.5 text-xs text-slate-400">
                                Diajukan {{ $pp->diajukan_pada?->translatedFormat('d M Y H:i') ?? '-' }}
                            </p>
                        </div>
                        <span class="self-start rounded-full px-2.5 py-1 text-xs font-semibold {{ $ppColor }}">
                            {{ $ppLabel }}
                        </span>
                    </div>

                    <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                        <div class="rounded-xl bg-slate-50 px-3 py-2 text-xs">
                            <p class="font-semibold uppercase tracking-wide text-slate-400">Pembimbing Aktif Saat Ini</p>
                            <p class="mt-0.5 font-semibold text-slate-700">
                                {{ $pembimbingLama ?? 'Belum ada pembimbing' }}
                            </p>
                        </div>
                        <div class="rounded-xl bg-cyan-50 px-3 py-2 text-xs">
                            <p class="font-semibold uppercase tracking-wide text-cyan-500">Dosen yang Diajukan</p>
                            <p class="mt-0.5 font-semibold text-cyan-800">
                                {{ $pp->dosen?->user?->name ?? '-' }}
                                <span class="font-normal text-cyan-600">({{ $pp->dosen?->nidn ?? '-' }})</span>
                            </p>
                        </div>
                    </div>

                    @if ($pp->alasan)
                        <div class="mt-3 rounded-xl bg-slate-50 px-3 py-2 text-sm text-slate-600">
                            <span class="font-medium text-slate-700">Alasan:</span> {{ $pp->alasan }}
                        </div>
                    @endif

                    @if ($pp->catatan_kaprodi)
                        <div class="mt-2 rounded-xl {{ $pp->status === 'rejected' ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700' }} px-3 py-2 text-sm">
                            <span class="font-semibold">Catatan Kaprodi:</span> {{ $pp->catatan_kaprodi }}
                        </div>
                    @endif

                    @if ($pp->status === 'pending')
                        <div class="mt-4 flex gap-2">
                            <button wire:click="confirmAction({{ $pp->id }}, 'approved')"
                                class="rounded-xl bg-emerald-600 px-4 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">
                                Setujui
                            </button>
                            <button wire:click="confirmAction({{ $pp->id }}, 'rejected')"
                                class="rounded-xl bg-rose-600 px-4 py-1.5 text-xs font-semibold text-white hover:bg-rose-700">
                                Tolak
                            </button>
                        </div>
                    @endif
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-sm text-slate-400">
                    Belum ada pengajuan dosen pembimbing dari mahasiswa.
                </div>
            @endforelse
        </div>

        @if ($pengajuanPembimbingList->hasPages())
            <div class="mt-4">{{ $pengajuanPembimbingList->links('vendor.pagination.tailwind') }}</div>
        @endif
    </section>

    <livewire:components.modal name="kaprodi-aksi-pembimbing">
        <div class="w-full">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">
                        {{ $actionType === 'approved' ? 'Setujui Pengajuan Pembimbing' : 'Tolak Pengajuan Pembimbing' }}
                    </h3>
                    <p class="mt-0.5 text-xs text-slate-400">
                        {{ $actionType === 'approved'
                            ? 'Jika disetujui, dosen pembimbing mahasiswa akan otomatis diganti.'
                            : 'Berikan alasan penolakan agar mahasiswa dapat memahami keputusan ini.' }}
                    </p>
                </div>
                <button wire:click="$dispatch('close-modal', { name: 'kaprodi-aksi-pembimbing' })"
                    class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mt-4 border-t border-slate-100"></div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-slate-700">
                    Catatan <span class="text-slate-400">(opsional)</span>
                </label>
                <textarea wire:model="catatanKaprodi" rows="3"
                    placeholder="Tambahkan catatan untuk mahasiswa..."
                    class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-100"></textarea>
                @error('catatanKaprodi') <x-ui.validation-error :message="$message" /> @enderror
            </div>

            <div class="mt-5 flex justify-end gap-3 border-t border-slate-100 pt-4">
                <button wire:click="$dispatch('close-modal', { name: 'kaprodi-aksi-pembimbing' })"
                    class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Batal
                </button>
                <button wire:click="prosesAksiPembimbing"
                    class="rounded-xl px-5 py-2 text-sm font-semibold text-white
                        {{ $actionType === 'approved' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700' }}">
                    {{ $actionType === 'approved' ? 'Ya, Setujui' : 'Ya, Tolak' }}
                </button>
            </div>
        </div>
    </livewire:components.modal>
</div>
