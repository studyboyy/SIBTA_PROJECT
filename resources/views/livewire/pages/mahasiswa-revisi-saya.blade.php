<div class="space-y-6">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-amber-900 to-blue-800 px-6 py-7 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-amber-100/80">Portal Mahasiswa</p>
                <h1 class="mt-2 text-2xl font-semibold sm:text-3xl">Revisi Saya</h1>
                <p class="mt-2 max-w-2xl text-sm text-amber-100 sm:text-base">
                    Fokus pada dokumen yang perlu diperbaiki, catatan dosen, dan pengiriman ulang hasil revisi.
                </p>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-amber-100/70">Revisi</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['perlu_revisi'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-amber-100/70">Menunggu</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['menunggu'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-amber-100/70">Selesai</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['selesai'] }}</p>
                </div>
            </div>
        </div>
    </section>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Daftar Dokumen</h2>
                <p class="text-sm text-slate-500">Dokumen revisi/ditolak ditampilkan sebagai prioritas.</p>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="searchRevisiSaya" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Cari</label>
                    <input id="searchRevisiSaya" type="text" wire:model.live.debounce.350ms="search"
                        placeholder="Nama dokumen atau catatan..."
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-amber-500 focus:ring-amber-500" />
                </div>
                <div>
                    <label for="statusRevisiSaya" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Status</label>
                    <select id="statusRevisiSaya" wire:model.live="status"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-amber-500 focus:ring-amber-500">
                        <option value="perlu_revisi">Perlu revisi</option>
                        <option value="menunggu">Menunggu review</option>
                        <option value="selesai">Selesai</option>
                        <option value="">Semua</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <x-ui.show-entries wire:model.live="perPage" class="focus:border-amber-500 focus:ring-amber-500" />
        </div>

        <div class="mt-4 space-y-3">
            @forelse ($dokumenList as $dokumen)
                <article wire:key="mahasiswa-revisi-{{ $dokumen->id }}" class="rounded-2xl border border-slate-200 p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $dokumen->bab ?? \App\Support\SidangDocumentCatalog::label($dokumen->jenis_dokumen) }}</p>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ $dokumen->created_at?->translatedFormat('d M Y H:i') }}
                                @if ($dokumen->revisi_requested_at)
                                    · Revisi diminta {{ $dokumen->revisi_requested_at?->translatedFormat('d M Y H:i') }}
                                @endif
                            </p>
                        </div>
                        <span @class([
                            'w-fit rounded-full px-2.5 py-1 text-xs font-semibold',
                            'bg-amber-100 text-amber-700' => $dokumen->status === 'pending',
                            'bg-blue-100 text-blue-700' => $dokumen->status === 'revisi',
                            'bg-emerald-100 text-emerald-700' => in_array($dokumen->status, ['approved', 'disetujui'], true),
                            'bg-rose-100 text-rose-700' => in_array($dokumen->status, ['ditolak', 'rejected'], true),
                        ])>
                            {{ ucfirst($dokumen->status ?? 'pending') }}
                        </span>
                    </div>

                    <div class="mt-3 rounded-xl bg-slate-50 px-3 py-2 text-sm text-slate-600">
                        <span class="font-semibold text-slate-700">Catatan dosen:</span>
                        {{ $dokumen->catatan ?: 'Belum ada catatan.' }}
                    </div>

                    <div class="mt-3 flex flex-wrap gap-2">
                        @if ($dokumen->file)
                            <a href="{{ Storage::url($dokumen->file) }}" target="_blank"
                                class="rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                                Buka Dokumen Saya
                            </a>
                        @endif
                        @if ($dokumen->reviewer_markup_file)
                            <a href="{{ Storage::url($dokumen->reviewer_markup_file) }}" target="_blank"
                                class="rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                File Revisi Bertanda
                            </a>
                        @endif
                    </div>

                    @if (in_array(strtolower((string) $dokumen->status), ['revisi', 'ditolak', 'rejected'], true))
                        <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Upload Hasil Revisi</label>
                            <div class="flex flex-col gap-2 sm:flex-row">
                                <input type="file" wire:model="revisiFiles.{{ $dokumen->id }}"
                                    class="block w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-700 focus:border-amber-500 focus:ring-amber-500" />
                                <button type="button" wire:click="kirimRevisi({{ $dokumen->id }})"
                                    class="rounded-xl bg-amber-600 px-4 py-2 text-xs font-semibold text-white hover:bg-amber-700">
                                    Kirim Revisi
                                </button>
                            </div>
                            @error('revisiFiles.' . $dokumen->id)
                                <x-ui.validation-error :message="$message" />
                            @enderror
                        </div>
                    @endif

                    @if ($dokumen->versions->isNotEmpty())
                        <details class="mt-3 rounded-xl border border-slate-200 bg-white p-3">
                            <summary class="cursor-pointer text-sm font-semibold text-slate-700">Timeline dokumen</summary>
                            <div class="mt-3 space-y-2">
                                @foreach ($dokumen->versions->take(5) as $version)
                                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-600">
                                        <p class="font-semibold text-slate-800">{{ ucfirst(str_replace('_', ' ', $version->action)) }}</p>
                                        <p>{{ $version->created_at?->translatedFormat('d M Y H:i') }} · {{ $version->uploader?->name ?? ucfirst($version->uploader_role) }}</p>
                                        @if ($version->note)
                                            <p class="mt-1">Catatan: {{ $version->note }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </details>
                    @endif
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-sm text-slate-500">
                    Tidak ada dokumen sesuai filter.
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $dokumenList->links('vendor.pagination.tailwind') }}
        </div>
    </section>
</div>
