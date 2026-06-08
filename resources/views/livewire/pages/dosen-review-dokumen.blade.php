<div class="space-y-6">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-blue-900 to-indigo-800 px-6 py-7 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-100/80">Panel Dosen</p>
                <h1 class="mt-2 text-2xl font-semibold sm:text-3xl">Review Dokumen TA</h1>
                <p class="mt-2 max-w-2xl text-sm text-blue-100 sm:text-base">
                    Tinjau dokumen mahasiswa, beri catatan revisi, setujui, atau tolak berkas yang belum sesuai.
                </p>
                <div
                    class="mt-3 inline-flex rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold text-blue-50 backdrop-blur">
                    Dosen: {{ $dosen->user?->name ?? '-' }}
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-blue-100/70">Total Data</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $dokumenList->total() }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-blue-100/70">Halaman Ini</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $dokumenList->count() }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-blue-100/70">Per Halaman</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $perPage }}</p>
                </div>
            </div>
        </div>
    </section>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <section class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Dokumen Mahasiswa</h2>
                <p class="text-sm text-slate-500">Dokumen pending dan revisi ditampilkan lebih dulu.</p>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="searchReviewDokumen" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Cari</label>
                    <input id="searchReviewDokumen" type="text" wire:model.live.debounce.350ms="search"
                        placeholder="Nama, NIM, jenis, catatan..."
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500" />
                </div>
                <div>
                    <label for="statusReviewDokumen" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Status</label>
                    <select id="statusReviewDokumen" wire:model.live="status"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua</option>
                        <option value="pending">Pending</option>
                        <option value="revisi">Revisi</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <x-ui.show-entries wire:model.live="perPage" class="focus:border-blue-500 focus:ring-blue-500" />
        </div>

        <div class="mt-4 space-y-3">
            @forelse ($dokumenList as $doc)
                <article wire:key="dosen-review-dokumen-{{ $doc->id }}" class="rounded-2xl border border-slate-200 p-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $doc->mahasiswa?->user?->name ?? 'Mahasiswa' }}</p>
                            <p class="text-xs text-slate-500">
                                {{ $doc->mahasiswa?->nim ?? '-' }} · {{ $doc->mahasiswa?->programStudi?->name ?? $doc->mahasiswa?->prodi }}
                            </p>
                            <p class="mt-2 text-sm font-medium text-slate-800">
                                {{ \App\Support\SidangDocumentCatalog::label($doc->jenis_dokumen) }} · {{ $doc->bab ?? '-' }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">{{ $doc->created_at?->translatedFormat('d M Y H:i') }}</p>
                        </div>
                        <span @class([
                            'inline-flex w-fit rounded-full px-2.5 py-1 text-xs font-semibold',
                            'bg-amber-100 text-amber-700' => $doc->status === 'pending',
                            'bg-blue-100 text-blue-700' => $doc->status === 'revisi',
                            'bg-emerald-100 text-emerald-700' => $doc->status === 'disetujui',
                            'bg-rose-100 text-rose-700' => $doc->status === 'ditolak',
                        ])>
                            {{ ucfirst($doc->status ?? 'pending') }}
                        </span>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-2">
                        @if ($doc->file)
                            <a href="{{ Storage::url($doc->file) }}" target="_blank"
                                class="rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                                Buka Dokumen
                            </a>
                        @endif
                        @if ($doc->reviewer_markup_file)
                            <a href="{{ Storage::url($doc->reviewer_markup_file) }}" target="_blank"
                                class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200">
                                File Revisi Bertanda
                            </a>
                        @endif
                    </div>

                    <div class="mt-4 grid gap-3 lg:grid-cols-[1fr_260px]">
                        <div>
                            <textarea wire:model="catatanDokumen.{{ $doc->id }}" rows="3"
                                placeholder="Catatan review untuk mahasiswa"
                                class="block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500"></textarea>
                            @error('catatanDokumen.' . $doc->id)
                                <x-ui.validation-error :message="$message" />
                            @enderror
                            @if ($doc->catatan)
                                <p class="mt-2 rounded-xl bg-slate-50 px-3 py-2 text-xs text-slate-600">
                                    Catatan terakhir: {{ $doc->catatan }}
                                </p>
                            @endif
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">File Revisi Bertanda</label>
                            <input type="file" wire:model="reviewerMarkupFiles.{{ $doc->id }}"
                                class="block w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-700 focus:border-blue-500 focus:ring-blue-500" />
                            @error('reviewerMarkupFiles.' . $doc->id)
                                <x-ui.validation-error :message="$message" />
                            @enderror
                        </div>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-2">
                        <button type="button" wire:click="setDokumenStatus({{ $doc->id }}, 'disetujui')"
                            class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">
                            Setujui
                        </button>
                        <button type="button" wire:click="mintaRevisi({{ $doc->id }})"
                            class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                            Minta Revisi
                        </button>
                        <button type="button" wire:click="setDokumenStatus({{ $doc->id }}, 'ditolak')"
                            class="rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-700">
                            Tolak
                        </button>
                    </div>

                    @if ($doc->versions->isNotEmpty())
                        <details class="mt-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <summary class="cursor-pointer text-sm font-semibold text-slate-700">Timeline dokumen</summary>
                            <div class="mt-3 space-y-2">
                                @foreach ($doc->versions->take(5) as $version)
                                    <div class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-600">
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
                    Belum ada dokumen sesuai filter.
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $dokumenList->links('vendor.pagination.tailwind') }}
        </div>
    </section>
</div>
