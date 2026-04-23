<div class="space-y-8">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-emerald-900 to-teal-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-emerald-100/80">Portal Mahasiswa</p>
            <h1 class="text-3xl font-semibold sm:text-4xl">Pengajuan Sidang</h1>
            <p class="max-w-2xl text-sm text-emerald-100 sm:text-base">Ajukan sidang setelah seluruh syarat kelengkapan
                terpenuhi.</p>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 2xl:grid-cols-[1fr_1fr]">
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Checklist Kelayakan</h2>
            <p class="mt-1 text-sm text-slate-500">Semua item wajib terpenuhi sebelum pengajuan.</p>

            <div class="mt-5 space-y-3">
                @php
                    $labels = [
                        'proposal' => 'Dokumen proposal disetujui',
                        'laporan_ta' => 'Dokumen laporan TA disetujui',
                        'jurnal' => 'Dokumen jurnal disetujui',
                        'bebas_lab' => 'Dokumen bebas lab disetujui',
                        'bebas_pustaka' => 'Dokumen bebas pustaka disetujui',
                        'belum_dijadwalkan' => 'Belum memiliki jadwal sidang',
                    ];
                @endphp
                @foreach ($labels as $key => $label)
                    <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
                        <p class="text-sm text-slate-700">{{ $label }}</p>
                        @if ($checklist[$key])
                            <span
                                class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Terpenuhi</span>
                        @else
                            <span
                                class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">Belum</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Form Pengajuan</h2>
            <div class="mt-2 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Status Dosen</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ ucfirst($pengajuan->status_dosen ?? 'pending') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Status Kaprodi</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ ucfirst($pengajuan->status_kaprodi ?? 'pending') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Status Admin</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ ucfirst($pengajuan->status ?? 'belum mengajukan') }}</p>
                    <p class="mt-1 text-xs text-slate-500">Gelombang: {{ $pengajuan->gelombang ?? '-' }}</p>
                </div>
            </div>

            @if ($pengajuan?->catatan_admin)
                <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    Catatan Admin: {{ $pengajuan->catatan_admin }}
                </div>
            @endif

            @if ($pengajuan?->catatan_dosen)
                <div class="mt-4 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                    Catatan Dosen: {{ $pengajuan->catatan_dosen }}
                </div>
            @endif

            @if ($pengajuan?->catatan_kaprodi)
                <div class="mt-4 rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-800">
                    Catatan Kaprodi: {{ $pengajuan->catatan_kaprodi }}
                </div>
            @endif

            <form wire:submit.prevent="submit" class="mt-5 space-y-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Catatan Pengajuan (opsional)</label>
                    <textarea wire:model="catatan_mahasiswa" rows="5" placeholder="Contoh: semua dokumen final telah diperbaiki"
                        class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100"></textarea>
                    @error('catatan_mahasiswa')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" @disabled(!$isEligible)
                        class="rounded-2xl px-5 py-3 text-sm font-semibold text-white {{ $isEligible ? 'bg-emerald-600 hover:bg-emerald-500' : 'bg-slate-400 cursor-not-allowed' }}">
                        {{ $pengajuan ? 'Kirim Ulang Pengajuan' : 'Ajukan Sidang' }}
                    </button>
                </div>
            </form>

            <p class="mt-3 text-xs text-slate-500">Tanggal pengajuan terakhir:
                {{ $pengajuan?->diajukan_pada?->translatedFormat('d M Y H:i') ?? '-' }}</p>
        </article>
    </section>
</div>
