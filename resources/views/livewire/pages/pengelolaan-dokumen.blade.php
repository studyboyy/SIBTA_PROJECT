<div class="mx-auto max-w-7xl p-6">
    {{-- HEADER PAGE --}}
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Manajemen Dokumen Tugas Akhir (Admin)</h1>
        <p class="mt-1 text-sm text-gray-600">Kelola kelengkapan berkas dan arsip digital tugas akhir mahasiswa.</p>
    </div>

    {{-- ALERT NOTIFIKASI --}}
    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-sm font-medium text-green-700">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-sm font-medium text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="space-y-6">
        {{-- SECTION 1: KELENGKAPAN BERKAS TA --}}
        <section class="rounded-xl bg-white p-6 shadow">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Kelengkapan Berkas TA</h2>
                <p class="text-sm text-gray-600">Checklist berkas sidang berdasarkan dokumen yang sudah disetujui dosen
                    pembimbing.</p>
            </div>

            {{-- FILTER SECTION 1 --}}
            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" wire:model.live.debounce.400ms="searchKelengkapan"
                        placeholder="Cari nama mahasiswa / NIM"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                    <select wire:model.live="statusKelengkapan"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">Semua</option>
                        <option value="lengkap">Lengkap</option>
                        <option value="belum-lengkap">Belum lengkap</option>
                    </select>
                    <button type="button" wire:click="resetFilterKelengkapan"
                        class="mt-2 rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                        Reset Filter
                    </button>
                </div>
            </div>

            {{-- CARD/TABLE HYBRID LIST --}}
            @if ($kelengkapan->isEmpty())
                <div
                    class="flex items-center justify-center rounded-xl border border-dashed border-gray-300 py-10 text-center text-sm text-gray-500">
                    Belum ada data
                </div>
            @else
                <div class="mb-4">
                    <x-ui.show-entries wire:model.live="kelengkapanPerPage"
                        class="focus:border-blue-500 focus:ring-blue-500" />
                </div>

                <div class="space-y-4">
                    @foreach ($kelengkapan as $item)
                        <div wire:key="kelengkapan-{{ $item['id'] }}" class="rounded-xl border border-gray-200 p-4">
                            <div class="mb-3 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $item['nama'] }}</h3>
                                    <p class="text-sm text-gray-600">NIM: {{ $item['nim'] }}</p>
                                </div>
                                <span
                                    class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $item['status_key'] === 'lengkap' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $item['status_label'] }}
                                </span>
                                <div class="mb-1 flex items-center justify-between text-xs text-gray-500">
                                    <span>Progress Kelengkapan</span>
                                    <span>{{ $item['progress'] }}%</span>
                                </div>
                                <div class="h-2.5 w-full rounded-full bg-gray-200">
                                    <div class="h-2.5 rounded-full bg-blue-500" style="width: {{ $item['progress'] }}%">
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-5">
                                @foreach ($item['checklist'] as $label => $ok)
                                    <div
                                        class="flex items-center gap-2 rounded-lg border border-gray-100 px-3 py-2 text-sm text-gray-600">
                                        @if ($ok)
                                            <svg class="h-4 w-4 shrink-0 text-green-600" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M16.704 5.29a1 1 0 010 1.42l-7.5 7.5a1 1 0 01-1.415 0l-3.5-3.5a1 1 0 111.415-1.42l2.793 2.79 6.793-6.79a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4 shrink-0 text-red-600" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                        <span>{{ $label }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4">
                                <button wire:click="lihatDetail({{ $item['id'] }})"
                                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                    Lihat Detail
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    {{ $kelengkapan->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </section>

        {{-- SECTION 2: ARSIP DIGITAL TA --}}
        <section class="rounded-xl bg-white p-6 shadow">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Arsip Digital TA</h2>
                <p class="text-sm text-gray-600">Repository dokumen tugas akhir mahasiswa yang sudah final.</p>
            </div>

            {{-- FILTER SECTION 2 --}}
            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Search Arsip</label>
                    <input type="text" wire:model.live.debounce.400ms="searchArsip"
                        placeholder="Cari judul / mahasiswa"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Tahun</label>
                    <select wire:model="filterTahun"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">Semua Tahun</option>
                        @foreach ($tahunList as $tahun)
                            <option value="{{ $tahun }}">{{ $tahun }}</option>
                        @endforeach
                    </select>
                    <button type="button" wire:click="resetFilterArsip"
                        class="mt-2 rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                        Reset Filter
                    </button>
                </div>
            </div>

            {{-- GRID CARD ARSIP --}}
            @if ($arsip->isEmpty())
                <div
                    class="flex items-center justify-center rounded-xl border border-dashed border-gray-300 py-10 text-center text-sm text-gray-500">
                    Belum ada data
                </div>
            @else
                <div class="mb-4">
                    <x-ui.show-entries wire:model.live="arsipPerPage"
                        class="focus:border-blue-500 focus:ring-blue-500" />
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($arsip as $item)
                        <div wire:key="arsip-{{ $item['id'] }}"
                            class="rounded-xl bg-white p-4 shadow-md transition hover:-translate-y-0.5 hover:shadow-lg">
                            <div class="mb-3 flex items-start gap-2">
                                <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-500" viewBox="0 0 24 24"
                                    fill="currentColor">
                                    <path
                                        d="M6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6H6zm7 1.5L18.5 9H13V3.5z" />
                                </svg>
                                <h3 class="line-clamp-2 text-sm font-semibold text-gray-900"
                                    title="{{ $item['judul'] }}">
                                    {{ $item['judul'] }}
                                </h3>
                            </div>
                            <p class="text-sm text-gray-600">{{ $item['mahasiswa'] }}</p>
                            <p class="mb-2 text-sm text-gray-500">Tahun: {{ $item['tahun'] }}</p>

                            <div class="mb-4 flex flex-wrap gap-2">
                                @foreach ($item['keywords'] as $keyword)
                                    <span
                                        class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs text-gray-600">{{ $keyword }}</span>
                                @endforeach
                            </div>

                            <div class="flex items-center gap-2">
                                <button wire:click="preview({{ $item['id'] }})"
                                    class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">
                                    Preview
                                </button>
                                <button wire:click="download('{{ $item['file'] }}')" wire:loading.attr="disabled"
                                    wire:target="download('{{ $item['file'] }}')"
                                    class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700 disabled:opacity-60">
                                    <svg wire:loading wire:target="download('{{ $item['file'] }}')"
                                        class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                                    </svg>
                                    Download
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    {{ $arsip->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </section>
    </div>

    {{-- MODAL DETAIL KELENGKAPAN BERKAS --}}
    @if ($detailDokumen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="w-full max-w-2xl rounded-xl bg-white p-6 shadow-xl">
                <div class="mb-4 flex items-start justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Detail Dokumen: {{ $detailDokumen['nama'] }}
                        </h3>
                    </div>

                    <div class="space-y-2">
                        @foreach ($detailDokumen['files'] as $file)
                            <div class="flex items-center justify-between rounded-lg border border-gray-200 px-3 py-2">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $file['nama'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $file['jenis'] }}</p>
                                    <p class="mt-1 text-xs text-gray-500">Status dosen: {{ ucfirst($file['status']) }}
                                    </p>
                                    @if ($file['catatan'] !== '')
                                        <p class="mt-1 text-xs text-amber-700">Catatan dosen: {{ $file['catatan'] }}
                                        </p>
                                    @endif
                                </div>
                                <button wire:click="download('{{ $file['nama'] }}')"
                                    class="rounded-md bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">
                                    Download
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                        Halaman ini hanya memantau kelengkapan berkas. Persetujuan dokumen tetap dilakukan oleh dosen
                        pembimbing di kontrol bimbingan.
                    </div>
                </div>
            </div>
    @endif

    {{-- MODAL PREVIEW PDF ARSIP --}}
    @if ($previewDokumen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="w-full max-w-4xl rounded-xl bg-white p-6 shadow-xl">
                <div class="mb-4 flex items-start justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Preview Arsip PDF</h3>
                        <p class="text-sm text-gray-500">{{ $previewDokumen['judul'] }}</p>
                    </div>
                    <button wire:click="tutupPreview" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <div
                    class="flex h-80 items-center justify-center rounded-xl border border-dashed border-gray-300 bg-gray-50 text-sm text-gray-500">
                    Preview PDF: {{ $previewDokumen['file'] }}
                </div>

                <div class="mt-4 flex justify-end">
                    <button wire:click="download('{{ $previewDokumen['file'] }}')"
                        class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                        Download
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
