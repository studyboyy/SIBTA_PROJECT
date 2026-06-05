<div class="space-y-8">

    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-indigo-900 to-blue-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-indigo-100/80">Panel Admin</p>
            <h1 class="text-3xl font-semibold sm:text-4xl">Pengelolaan Dokumen TA</h1>
            <p class="max-w-2xl text-sm text-indigo-100 sm:text-base">
                Pantau kelengkapan berkas sidang per mahasiswa dan akses arsip digital tugas akhir yang sudah final.
            </p>
        </div>
    </section>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- KELENGKAPAN BERKAS TA --}}
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Kelengkapan Berkas TA</h2>
                <p class="mt-1 text-sm text-slate-500">Checklist berkas sidang berdasarkan dokumen yang sudah disetujui dosen pembimbing.</p>
            </div>
            <div class="grid w-full gap-3 sm:grid-cols-3 lg:max-w-2xl">
                <div class="sm:col-span-2">
                    <input type="text" wire:model.live.debounce.400ms="searchKelengkapan"
                        placeholder="Cari nama mahasiswa atau NIM..."
                        class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                </div>
                <div class="flex gap-2">
                    <select wire:model.live="statusKelengkapan"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="">Semua</option>
                        <option value="lengkap">Lengkap</option>
                        <option value="belum-lengkap">Belum lengkap</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-3">
            <x-ui.show-entries wire:model.live="kelengkapanPerPage" class="focus:border-blue-500 focus:ring-blue-500" />
            <button type="button" wire:click="resetFilterKelengkapan"
                class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                Reset Filter
            </button>
        </div>

        @if ($kelengkapan->isEmpty())
            <div class="mt-6 rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-sm text-slate-500">
                Belum ada data kelengkapan berkas.
            </div>
        @else
            <div class="mt-5 space-y-3">
                @foreach ($kelengkapan as $item)
                    <div wire:key="kelengkapan-{{ $item['id'] }}" class="rounded-2xl border border-slate-200 p-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $item['nama'] }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">NIM: {{ $item['nim'] }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-1.5">
                                    <div class="h-2 w-24 rounded-full bg-slate-100">
                                        <div class="h-2 rounded-full bg-blue-500" style="width: {{ $item['progress'] }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-slate-600">{{ $item['progress'] }}%</span>
                                </div>
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold
                                    {{ $item['status_key'] === 'lengkap' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $item['status_label'] }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($item['checklist'] as $label => $ok)
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium
                                    {{ $ok ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    @if ($ok)
                                        <svg class="h-3 w-3 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.5 7.5a1 1 0 01-1.415 0l-3.5-3.5a1 1 0 111.415-1.42l2.793 2.79 6.793-6.79a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <svg class="h-3 w-3 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                    {{ $label }}
                                </span>
                            @endforeach
                        </div>

                        <div class="mt-3">
                            <button wire:click="lihatDetail({{ $item['id'] }})"
                                class="rounded-xl bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">
                                Lihat Detail Dokumen
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $kelengkapan->links('vendor.pagination.tailwind') }}</div>
        @endif
    </section>

    {{-- ARSIP DIGITAL TA --}}
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Arsip Digital TA</h2>
                <p class="mt-1 text-sm text-slate-500">Repository dokumen tugas akhir mahasiswa yang sudah final.</p>
            </div>
            <div class="grid w-full gap-3 sm:grid-cols-3 lg:max-w-2xl">
                <div class="sm:col-span-2">
                    <input type="text" wire:model.live.debounce.400ms="searchArsip"
                        placeholder="Cari judul atau nama mahasiswa..."
                        class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                </div>
                <div class="flex gap-2">
                    <select wire:model.live="filterTahun"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="">Semua Tahun</option>
                        @foreach ($tahunList as $tahun)
                            <option value="{{ $tahun }}">{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-3">
            <x-ui.show-entries wire:model.live="arsipPerPage" class="focus:border-blue-500 focus:ring-blue-500" />
            <button type="button" wire:click="resetFilterArsip"
                class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                Reset Filter
            </button>
        </div>

        @if ($arsip->isEmpty())
            <div class="mt-6 rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-sm text-slate-500">
                Belum ada arsip digital TA.
            </div>
        @else
            <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($arsip as $item)
                    <div wire:key="arsip-{{ $item['id'] }}"
                        class="rounded-2xl border border-slate-200 p-4 transition hover:border-slate-300 hover:shadow-sm">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 flex-shrink-0 rounded-xl bg-red-50 p-2 text-red-500">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6H6zm7 1.5L18.5 9H13V3.5z" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="line-clamp-2 text-sm font-semibold text-slate-900" title="{{ $item['judul'] }}">
                                    {{ $item['judul'] }}
                                </p>
                                <p class="mt-1 text-xs text-slate-500">{{ $item['mahasiswa'] }} • {{ $item['tahun'] }}</p>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @foreach ($item['keywords'] as $keyword)
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ $keyword }}</span>
                            @endforeach
                        </div>

                        <div class="mt-4 flex gap-2">
                            <button wire:click="preview({{ $item['id'] }})"
                                class="rounded-xl border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                                Preview
                            </button>
                            <button wire:click="download('{{ $item['file'] }}')" wire:loading.attr="disabled"
                                wire:target="download('{{ $item['file'] }}')"
                                class="inline-flex items-center gap-1 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 disabled:opacity-60">
                                <svg wire:loading wire:target="download('{{ $item['file'] }}')"
                                    class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                                </svg>
                                Download
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $arsip->links('vendor.pagination.tailwind') }}</div>
        @endif
    </section>

    {{-- MODAL DETAIL KELENGKAPAN --}}
    @if ($detailDokumen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-2xl rounded-3xl bg-white p-6 shadow-2xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Detail Dokumen</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ $detailDokumen['nama'] }} — NIM: {{ $detailDokumen['nim'] }}</p>
                    </div>
                    <button wire:click="tutupDetail"
                        class="rounded-xl border border-slate-200 p-2 text-slate-500 hover:bg-slate-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mt-4 space-y-2 max-h-80 overflow-y-auto">
                    @forelse ($detailDokumen['files'] as $file)
                        <div class="flex items-start justify-between gap-3 rounded-2xl border border-slate-200 px-4 py-3">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-slate-800 truncate">{{ $file['nama'] }}</p>
                                <p class="text-xs text-slate-500">{{ $file['jenis'] }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">Status: <span class="font-medium {{ in_array(strtolower($file['status']), ['approved', 'disetujui']) ? 'text-emerald-700' : 'text-amber-700' }}">{{ ucfirst($file['status']) }}</span></p>
                                @if ($file['catatan'] !== '')
                                    <p class="mt-0.5 text-xs text-amber-700">Catatan dosen: {{ $file['catatan'] }}</p>
                                @endif
                            </div>
                            <button wire:click="download('{{ $file['nama'] }}')"
                                class="shrink-0 rounded-xl bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">
                                Download
                            </button>
                        </div>
                    @empty
                        <p class="py-4 text-center text-sm text-slate-500">Belum ada file yang tersedia.</p>
                    @endforelse
                </div>

                <div class="mt-4 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                    Persetujuan dokumen dilakukan oleh dosen pembimbing di halaman Kontrol Bimbingan.
                </div>

                <div class="mt-5 flex justify-end">
                    <button wire:click="tutupDetail"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL PREVIEW ARSIP --}}
    @if ($previewDokumen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-4xl rounded-3xl bg-white p-6 shadow-2xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Preview Arsip</h3>
                        <p class="mt-1 text-sm text-slate-500 line-clamp-2">{{ $previewDokumen['judul'] }}</p>
                    </div>
                    <button wire:click="tutupPreview"
                        class="rounded-xl border border-slate-200 p-2 text-slate-500 hover:bg-slate-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mt-4 flex h-80 items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 text-sm text-slate-500">
                    Preview PDF: {{ $previewDokumen['file'] }}
                </div>

                <div class="mt-5 flex justify-end gap-3">
                    <button wire:click="tutupPreview"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Tutup
                    </button>
                    <button wire:click="download('{{ $previewDokumen['file'] }}')"
                        class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                        Download
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
