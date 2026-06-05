<div class="space-y-8">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-indigo-900 to-blue-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-indigo-100/80">Panel Dosen SIBTA</p>
            <h1 class="text-3xl font-semibold sm:text-4xl">Kontrol Bimbingan</h1>
            <p class="max-w-2xl text-sm text-indigo-100 sm:text-base">
                Akses revisi dokumen mahasiswa dan tentukan kelayakan sidang.
            </p>
        </div>
    </section>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama/NIM/catatan"
                class="rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100" />
            <select wire:model.live="dokumenStatus"
                class="rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                <option value="">Semua status dokumen</option>
                <option value="pending">Pending</option>
                <option value="disetujui">Disetujui</option>
                <option value="revisi">Revisi</option>
                <option value="ditolak">Ditolak</option>
            </select>
            <select wire:model.live="timelineAction"
                class="rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                <option value="">Semua event timeline</option>
                <option value="upload">Upload awal</option>
                <option value="resubmission">Kirim hasil revisi mahasiswa</option>
                <option value="review_revisi">Revisi bertanda dosen</option>
                <option value="status_update">Update status dosen</option>
            </select>
            <select wire:model.live="sidangStatus"
                class="rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                <option value="">Semua status sidang</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="revisi">Revisi</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Kontrol Revisi Dokumen</h2>
            <p class="text-xs text-slate-500">Dosen: {{ $dosen->user?->name ?? '-' }}</p>
        </div>

        <div class="space-y-3">
            <div>
                <x-ui.show-entries wire:model.live="dokumenPerPage"
                    class="focus:border-indigo-500 focus:ring-indigo-500" />
            </div>

            @forelse ($dokumenList as $doc)
                @php
                    $isApprovedDokumen = in_array(
                        strtolower((string) ($doc->status ?? '')),
                        ['disetujui', 'approved'],
                        true,
                    );
                    $isEditingDokumen = (bool) ($editingDokumenIds[$doc->id] ?? false);
                    $showDokumenActions = !$isApprovedDokumen || $isEditingDokumen;
                @endphp
                <article class="rounded-2xl border border-slate-200 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $doc->mahasiswa?->user?->name ?? 'Mahasiswa' }}
                            </p>
                            <p class="text-xs text-slate-500">{{ $doc->mahasiswa?->nim ?? '-' }} • {{ $doc->bab }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ $doc->created_at?->translatedFormat('d M Y H:i') }}</p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                            {{ ucfirst($doc->status ?? 'pending') }}
                        </span>
                    </div>

                    <div class="mt-2 flex flex-wrap gap-2">
                        <a href="{{ Storage::url($doc->file) }}" target="_blank"
                            class="inline-flex rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                            Buka Dokumen Mahasiswa
                        </a>
                    </div>

                    <details class="mt-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <summary class="cursor-pointer list-none text-sm font-semibold text-slate-700">
                            Lihat Detail Revisi, Komentar, dan Timeline
                        </summary>

                        <div class="mt-3 space-y-3 border-t border-slate-200 pt-3">
                            <div class="flex flex-wrap gap-2">
                                @if ($doc->reviewer_markup_file)
                                    <a href="{{ Storage::url($doc->reviewer_markup_file) }}" target="_blank"
                                        class="inline-flex rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                                        Buka File Revisi Bertanda
                                    </a>
                                @endif
                            </div>

                            @if (str_ends_with(strtolower($doc->file ?? ''), '.pdf'))
                                <div class="rounded-xl border border-slate-200 overflow-hidden">
                                    <iframe src="{{ Storage::url($doc->file) }}#toolbar=1" class="h-64 w-full"></iframe>
                                </div>
                            @endif

                            @if ($doc->reviewer_markup_file && str_ends_with(strtolower($doc->reviewer_markup_file), '.pdf'))
                                <div class="rounded-xl border border-blue-200 overflow-hidden">
                                    <iframe src="{{ Storage::url($doc->reviewer_markup_file) }}#toolbar=1"
                                        class="h-64 w-full"></iframe>
                                </div>
                            @endif

                            @if ($showDokumenActions)
                                <textarea wire:model="catatanDokumen.{{ $doc->id }}" rows="2" placeholder="Komentar/masukan dokumen"
                                    class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"></textarea>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Upload File Revisi
                                        Bertanda
                                        (coretan dosen)
                                    </label>
                                    <input type="file" wire:model="reviewerMarkupFiles.{{ $doc->id }}"
                                        class="block w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100" />
                                    @error('reviewerMarkupFiles.' . $doc->id)
                                        <x-ui.validation-error :message="$message" />
                                    @enderror
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <button wire:click="kirimRevisiDokumen({{ $doc->id }})"
                                        class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                                        Kirim Revisi Bertanda
                                    </button>
                                    <button wire:click="updateDokumenStatus({{ $doc->id }}, 'disetujui')"
                                        class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">
                                        ACC Revisi/BAB
                                    </button>
                                    <button wire:click="updateDokumenStatus({{ $doc->id }}, 'ditolak')"
                                        class="rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">
                                        Tolak
                                    </button>
                                    @if ($isApprovedDokumen)
                                        <button wire:click="toggleEditDokumen({{ $doc->id }})"
                                            class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                            Tutup Edit
                                        </button>
                                    @endif
                                </div>
                            @elseif ($isApprovedDokumen)
                                <div
                                    class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-700">
                                    Dokumen sudah ACC revisi. Klik Edit jika ingin membuka kembali form revisi dan aksi.
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <button wire:click="toggleEditDokumen({{ $doc->id }})"
                                        class="rounded-lg bg-slate-800 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">
                                        Edit
                                    </button>
                                </div>
                            @endif

                            @if ($doc->versions->isNotEmpty())
                                <div class="rounded-xl border border-slate-200 bg-white p-3">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-700">Timeline
                                        Dokumen</p>
                                    <div class="mt-2 space-y-2">
                                        @foreach ($doc->versions->take(6) as $version)
                                            <div
                                                class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-700">
                                                <div class="flex flex-wrap items-center justify-between gap-2">
                                                    <p class="font-semibold text-slate-800">
                                                        @if ($version->action === 'upload')
                                                            Upload Awal Mahasiswa
                                                        @elseif ($version->action === 'resubmission')
                                                            Kirim Hasil Revisi Mahasiswa
                                                        @elseif ($version->action === 'review_revisi')
                                                            Revisi Bertanda Dosen
                                                        @else
                                                            Update Status Dosen
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
                                                        {{ $version->note }}
                                                    </p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </details>
                </article>
            @empty
                <div
                    class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                    Belum ada dokumen revisi dari mahasiswa bimbingan.
                </div>
            @endforelse
        </div>

        <div class="mt-5">
            {{ $dokumenList->links('vendor.pagination.tailwind') }}
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-slate-900">ACC Kelayakan Sidang</h2>

        <div class="space-y-3">
            <div>
                <x-ui.show-entries wire:model.live="sidangPerPage"
                    class="focus:border-indigo-500 focus:ring-indigo-500" />
            </div>

            @forelse ($pengajuanSidangList as $pengajuan)
                <article class="rounded-2xl border border-slate-200 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-900">
                                {{ $pengajuan->mahasiswa?->user?->name ?? 'Mahasiswa' }}</p>
                            <p class="text-xs text-slate-500">{{ $pengajuan->mahasiswa?->nim ?? '-' }} • diajukan
                                {{ $pengajuan->diajukan_pada?->translatedFormat('d M Y H:i') ?? '-' }}</p>
                        </div>
                        <span
                            class="rounded-full px-3 py-1 text-xs font-semibold {{ $pengajuan->status_dosen === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($pengajuan->status_dosen === 'rejected' ? 'bg-red-100 text-red-700' : ($pengajuan->status_dosen === 'revisi' ? 'bg-orange-100 text-orange-700' : 'bg-amber-100 text-amber-700')) }}">
                            {{ ucfirst($pengajuan->status_dosen ?? 'pending') }}
                        </span>
                    </div>

                    <div class="mt-2 rounded-xl bg-slate-50 px-3 py-2 text-sm text-slate-600">
                        <span class="font-medium text-slate-700">Catatan Mahasiswa:</span>
                        {{ $pengajuan->catatan_mahasiswa ?: '-' }}
                    </div>

                    <p class="mt-2 text-xs text-slate-500">Status admin sidang:
                        {{ ucfirst($pengajuan->status ?? 'pending') }}
                        @if ($pengajuan->gelombang)
                            • Gelombang {{ $pengajuan->gelombang }}
                        @endif
                    </p>

                    <textarea wire:model="catatanSidang.{{ $pengajuan->id }}" rows="2"
                        placeholder="Catatan dosen untuk kelayakan sidang"
                        class="mt-3 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"></textarea>
                    @error('catatanSidang.' . $pengajuan->id)
                        <x-ui.validation-error :message="$message" />
                    @enderror

                    <p class="mt-1 text-xs text-slate-500">ACC kelayakan:
                        {{ $pengajuan->acc_kelayakan_at?->translatedFormat('d M Y H:i') ?? '-' }}</p>

                    @if ($pengajuan->status_dosen === 'approved' && $pengajuan->acc_kelayakan_at)
                        <div class="mt-3 rounded-xl border border-blue-200 bg-blue-50 px-3 py-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">
                                Tanda Tangan Digital
                            </p>
                            <p class="mt-1 text-xs text-blue-800">
                                {{ $this->signatureLabel('sidang') }}-{{ $this->buildDigitalSignature('sidang', $pengajuan->id, $pengajuan->acc_kelayakan_at, $dosen->user_id) }}
                            </p>
                            <p class="mt-1 text-[11px] text-blue-700/90">
                                Ditandatangani oleh {{ $dosen->user?->name ?? 'Dosen' }} pada
                                {{ $pengajuan->acc_kelayakan_at?->translatedFormat('d M Y H:i') }}
                            </p>
                        </div>
                    @endif

                    <div class="mt-3 flex flex-wrap gap-2">
                        @if ($pengajuan->status_dosen !== 'approved')
                        <button wire:click="updateSidangStatus({{ $pengajuan->id }}, 'approved')"
                            class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">
                            ACC Kelayakan Sidang
                        </button>
                        <button wire:click="updateSidangStatus({{ $pengajuan->id }}, 'revisi')"
                            class="rounded-lg bg-orange-500 px-3 py-2 text-xs font-semibold text-white hover:bg-orange-600">
                            Revisi
                        </button>
                        <button wire:click="updateSidangStatus({{ $pengajuan->id }}, 'rejected')"
                            class="rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">
                            Reject
                        </button>
                        @else
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs text-emerald-700 font-semibold">
                            Kelayakan sidang sudah di-ACC. Menunggu persetujuan kaprodi dan admin.
                        </div>
                        @endif
                    </div>
                </article>
            @empty
                <div
                    class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                    Belum ada pengajuan sidang dari mahasiswa bimbingan.
                </div>
            @endforelse
        </div>

        <div class="mt-5">
            {{ $pengajuanSidangList->links('vendor.pagination.tailwind') }}
        </div>
    </section>
</div>
