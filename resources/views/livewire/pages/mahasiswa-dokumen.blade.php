<div class="space-y-8">
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-blue-900 to-cyan-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-cyan-100/80">Dokumen Tugas Akhir</p>
            <h1 class="text-3xl font-semibold sm:text-4xl">Dokumen Saya</h1>
            <p class="max-w-2xl text-sm text-blue-100 sm:text-base">
                Unggah dokumen kelayakan sidang dan pantau status persetujuan dari dosen pembimbing.
            </p>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 2xl:grid-cols-[0.9fr_1.1fr]">
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <h2 class="text-base font-semibold text-slate-900">Kelengkapan Berkas Sidang</h2>
                <p class="mt-1 text-sm text-slate-500">Lima dokumen berikut wajib berstatus disetujui oleh dosen.</p>

                <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2">
                    @foreach ($requiredDocumentLabels as $key => $label)
                        <div
                            class="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                            <span class="text-slate-700">{{ $label }}</span>
                            @if ($kelayakanChecklist[$key] ?? false)
                                <span
                                    class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Layak</span>
                            @else
                                <span
                                    class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">Belum</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <h2 class="text-lg font-semibold text-slate-900">Upload Dokumen</h2>
            <p class="mt-1 text-sm text-slate-500">Format file: PDF, DOC, DOCX. Maksimal 5MB. Dokumen yang diunggah akan
                diperiksa dosen.</p>

            <form wire:submit.prevent="save" novalidate class="mt-6 space-y-5">
                <div>
                    <label for="document_type" class="block text-sm font-medium text-slate-700">Jenis Dokumen</label>
                    <select id="document_type" wire:model="documentType"
                        class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="">Pilih jenis dokumen</option>
                        @foreach ($documentOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('documentType')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                @if ($documentType === 'lainnya')
                    <div>
                        <label for="bab" class="block text-sm font-medium text-slate-700">Nama Dokumen</label>
                        <input id="bab" type="text" wire:model="bab"
                            placeholder="Contoh: Draft BAB 4, Lampiran Revisi"
                            class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                        @error('bab')
                            <x-ui.validation-error :message="$message" />
                        @enderror
                    </div>
                @else
                    <div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                        Dokumen ini akan menggantikan upload sebelumnya untuk jenis yang sama jika sudah pernah
                        diunggah.
                    </div>
                @endif

                <div>
                    <label for="file" class="block text-sm font-medium text-slate-700">File Dokumen</label>
                    <input id="file" type="file" wire:model="file"
                        class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                    @error('file')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-500">
                        Upload Dokumen
                    </button>
                </div>
            </form>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Riwayat Dokumen</h2>
                    <p class="mt-1 text-sm text-slate-500">NIM: {{ $mahasiswa->nim }} • {{ $mahasiswa->prodi }}</p>
                </div>
                <div class="w-full sm:w-64">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Filter Timeline</label>
                    <select wire:model.live="timelineAction"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="">Semua event</option>
                        <option value="upload">Upload awal</option>
                        <option value="resubmission">Kirim hasil revisi</option>
                        <option value="review_revisi">Revisi bertanda dosen</option>
                        <option value="status_update">Update status dosen</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 space-y-3">
                @forelse ($dokumenList as $dokumen)
                    @php
                        $isSelesai = in_array(strtolower((string) $dokumen->status), ['approved', 'disetujui'], true);
                    @endphp
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $dokumen->bab }}</p>
                                @if ($dokumen->jenis_dokumen)
                                    <p class="mt-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-blue-600">
                                        {{ $documentOptions[$dokumen->jenis_dokumen] ?? ucfirst(str_replace('_', ' ', $dokumen->jenis_dokumen)) }}
                                    </p>
                                @endif
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $dokumen->created_at?->translatedFormat('d M Y H:i') }}</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <a href="{{ Storage::url($dokumen->file) }}" target="_blank"
                                        class="inline-flex rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                        Lihat Dokumen Saya
                                    </a>
                                    @if ($dokumen->reviewer_markup_file)
                                        <a href="{{ Storage::url($dokumen->reviewer_markup_file) }}" target="_blank"
                                            class="inline-flex rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                            Lihat File Revisi Bertanda Dosen
                                        </a>
                                    @endif
                                </div>
                                <p class="mt-2 text-xs text-slate-600">
                                    Komentar reviewer: {{ $dokumen->catatan ?: 'Belum ada komentar.' }}
                                </p>
                            </div>
                            <span
                                class="rounded-full px-3 py-1 text-xs font-semibold {{ in_array(strtolower($dokumen->status), ['approved', 'disetujui']) ? 'bg-emerald-100 text-emerald-700' : (in_array(strtolower($dokumen->status), ['rejected', 'ditolak']) ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                                {{ ucfirst($dokumen->status) }}
                            </span>
                        </div>

                        @if ($isSelesai)
                            <details class="mt-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <summary class="cursor-pointer list-none text-sm font-semibold text-slate-700">
                                    Lihat Detail Dokumen Selesai
                                </summary>
                                <div class="mt-3 space-y-3 border-t border-slate-200 pt-3">
                        @endif

                        @if ($dokumen->reviewer_markup_file && str_ends_with(strtolower($dokumen->reviewer_markup_file), '.pdf'))
                            <div class="mt-3 rounded-xl border border-indigo-200 overflow-hidden">
                                <iframe src="{{ Storage::url($dokumen->reviewer_markup_file) }}#toolbar=1"
                                    class="h-64 w-full"></iframe>
                            </div>
                        @endif

                        @if (in_array(strtolower((string) $dokumen->status), ['revisi', 'ditolak', 'rejected'], true))
                            <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <p class="text-xs font-semibold text-slate-700">Kirim Dokumen Hasil Revisi</p>
                                <div class="mt-2 flex flex-col gap-2 sm:flex-row sm:items-center">
                                    <input type="file" wire:model="revisiFiles.{{ $dokumen->id }}"
                                        class="block w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                                    <button wire:click="kirimRevisi({{ $dokumen->id }})"
                                        class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                                        Kirim Hasil Revisi
                                    </button>
                                </div>
                                @error('revisiFiles.' . $dokumen->id)
                                    <x-ui.validation-error :message="$message" />
                                @enderror
                            </div>
                        @endif

                        @if ($dokumen->versions->isNotEmpty())
                            <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-700">Timeline Revisi
                                </p>
                                <div class="mt-2 space-y-2">
                                    @foreach ($dokumen->versions->take(6) as $version)
                                        <div
                                            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <p class="font-semibold text-slate-800">
                                                    @if ($version->action === 'upload')
                                                        Upload Awal
                                                    @elseif ($version->action === 'resubmission')
                                                        Kirim Hasil Revisi
                                                    @elseif ($version->action === 'review_revisi')
                                                        Revisi Bertanda Dosen
                                                    @else
                                                        Update Status
                                                    @endif
                                                </p>
                                                <p class="text-[11px] text-slate-500">
                                                    {{ $version->created_at?->translatedFormat('d M Y H:i') }}
                                                </p>
                                            </div>
                                            <p class="mt-1 text-[11px] text-slate-600">
                                                Oleh:
                                                {{ $version->uploader?->name ?? ucfirst($version->uploader_role) }}
                                                @if ($version->status_snapshot)
                                                    • Status: {{ ucfirst($version->status_snapshot) }}
                                                @endif
                                            </p>
                                            @if ($version->file)
                                                <a href="{{ Storage::url($version->file) }}" target="_blank"
                                                    class="mt-1 inline-flex text-[11px] font-semibold text-indigo-700 hover:text-indigo-900">
                                                    Lihat file pada versi ini
                                                </a>
                                            @endif
                                            @if ($version->note)
                                                <p class="mt-1 text-[11px] text-slate-600">Catatan:
                                                    {{ $version->note }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($isSelesai)
                    </div>
                    </details>
                @endif
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                Belum ada dokumen yang diunggah.
            </div>
            @endforelse
</div>

<div class="mt-6">
    <x-ui.show-entries wire:model.live="perPage" class="focus:border-blue-500 focus:ring-blue-500" />
</div>

<div class="mt-3">
    {{ $dokumenList->links('vendor.pagination.tailwind') }}
</div>
</article>
</section>
</div>
