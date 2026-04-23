<div class="max-w-7xl mx-auto p-6">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Penugasan Bimbingan</h1>
        <p class="mt-1 text-sm text-gray-500">Kelola pembimbing mahasiswa tugas akhir</p>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="mb-4 flex items-center gap-2 rounded-lg bg-green-100 px-4 py-3 text-sm font-medium text-green-700">
            <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 flex items-center gap-2 rounded-lg bg-red-100 px-4 py-3 text-sm font-medium text-red-700">
            <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-5a1 1 0 112 0v-4a1 1 0 10-2 0v4zm1-8a1 1 0 100 2 1 1 0 000-2z"
                    clip-rule="evenodd" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Grid: Form (kiri) + Table (kanan) --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- ========================
             FORM PENUGASAN
        ========================= --}}
        <div class="lg:col-span-1">
            <div class="rounded-xl bg-white shadow-md p-6">
                <h2 class="mb-4 text-base font-semibold text-gray-800">Tambah Penugasan</h2>

                <div class="space-y-4">

                    {{-- Multi-select Mahasiswa --}}
                    <div x-data="{ open: false, search: '' }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mahasiswa</label>
                        <p class="mb-2 text-xs text-gray-400">Mahasiswa yang sudah memiliki dosen pembimbing tidak akan
                            muncul di daftar ini.</p>

                        {{-- Trigger button --}}
                        <button type="button" @click="open = !open"
                            class="flex w-full items-center justify-between rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm hover:bg-gray-50 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <span>
                                @if (count($mahasiswa_ids) > 0)
                                    <span class="font-medium text-blue-600">{{ count($mahasiswa_ids) }} mahasiswa
                                        dipilih</span>
                                @else
                                    <span class="text-gray-400">-- Pilih Mahasiswa --</span>
                                @endif
                            </span>
                            <svg :class="open ? 'rotate-180' : ''" class="h-4 w-4 text-gray-400 transition-transform"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        {{-- Dropdown panel --}}
                        <div x-show="open" x-transition @click.outside="open = false"
                            class="relative z-10 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg">

                            {{-- Search inside panel --}}
                            <div class="border-b border-gray-100 p-2">
                                <input type="text" x-model="search" placeholder="Cari nama / NIM..."
                                    class="w-full rounded-md border border-gray-200 px-3 py-1.5 text-xs text-gray-700 focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-300" />
                            </div>

                            {{-- Checkbox list --}}
                            <div class="max-h-52 overflow-y-auto">
                                @if ($mahasiswas->isEmpty())
                                    <p class="px-3 py-4 text-center text-xs text-gray-400">Semua mahasiswa sudah
                                        memiliki dosen pembimbing.</p>
                                @else
                                    @foreach ($mahasiswas as $mhs)
                                        <label
                                            x-show="'{{ strtolower($mhs->user->name) }} {{ strtolower($mhs->nim) }}'.includes(search.toLowerCase())"
                                            class="flex cursor-pointer items-center gap-3 px-3 py-2 hover:bg-blue-50 transition-colors">
                                            <input type="checkbox" wire:model="mahasiswa_ids"
                                                value="{{ $mhs->id }}"
                                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-sm font-medium text-gray-800">
                                                    {{ $mhs->user->name }}</p>
                                                <p class="text-xs text-gray-400 font-mono">{{ $mhs->nim }}</p>
                                            </div>
                                        </label>
                                    @endforeach
                                @endif
                            </div>

                            {{-- Footer: clear + count --}}
                            <div class="flex items-center justify-between border-t border-gray-100 px-3 py-2">
                                <span class="text-xs text-gray-400">{{ count($mahasiswas) }} mahasiswa tersedia</span>
                                @if (count($mahasiswa_ids) > 0)
                                    <button type="button" wire:click="$set('mahasiswa_ids', [])"
                                        class="text-xs font-medium text-red-500 hover:text-red-600">
                                        Hapus pilihan
                                    </button>
                                @endif
                            </div>
                        </div>

                        @error('mahasiswa_ids')
                            <x-ui.validation-error :message="$message" />
                        @enderror
                    </div>

                    {{-- Dropdown Dosen --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dosen Pembimbing</label>
                        <p class="mb-2 text-xs text-gray-400">Pilih dosen berdasarkan kuota tersisa. Dosen yang sudah
                            penuh tidak bisa dipilih.</p>
                        <select wire:model="dosen_id"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">-- Pilih Dosen --</option>
                            @foreach ($dosens as $dsn)
                                <option class="flex justify-between w-full" value="{{ $dsn['id'] }}" @disabled($dsn['is_full'])>
                                    <span class="block">{{ $dsn['name'] }} ({{ $dsn['nidn'] }})</span>
                                    <span class="block text-green-400">Sisa {{ $dsn['sisa'] }}/{{ $dsn['kuota'] }}</span>
                                </option>
                            @endforeach
                        </select>

                        

                        @error('dosen_id')
                            <x-ui.validation-error :message="$message" />
                        @enderror
                    </div>

                    {{-- Tombol Submit --}}
                    <button wire:click="simpan" wire:loading.attr="disabled"
                        class="mt-2 flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed">
                        <svg wire:loading wire:target="simpan" class="h-4 w-4 animate-spin text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                        </svg>
                        <span wire:loading.remove wire:target="simpan">Tambah Penugasan</span>
                        <span wire:loading wire:target="simpan">Menyimpan...</span>
                    </button>

                </div>
            </div>
        </div>

        {{-- ========================
             TABLE LIST BIMBINGAN
        ========================= --}}
        <div class="lg:col-span-2">
            <div class="rounded-xl bg-white shadow-md p-6">

                {{-- Header + Search --}}
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-base font-semibold text-gray-800">Data Penugasan</h2>
                    <div class="relative w-full sm:w-64">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
                            </svg>
                        </span>
                        <input type="text" wire:model.live.debounce.400ms="search"
                            placeholder="Cari mahasiswa, NIM, atau dosen..."
                            class="w-full rounded-lg border border-gray-300 py-2 pl-9 pr-3 text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    </div>
                </div>

                {{-- Table --}}
                @if ($bimbingans->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                        <svg class="mb-3 h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12h6m-3-3v6M4.5 19.5l15-15M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                        </svg>
                        <p class="text-sm font-medium">Belum ada penugasan</p>
                    </div>
                @else
                    <div class="mb-4">
                        <x-ui.show-entries wire:model.live="perPage"
                            class="focus:border-blue-500 focus:ring-blue-500" />
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse text-sm">
                            <thead>
                                <tr
                                    class="bg-gray-100 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">
                                    <th class="rounded-tl-lg px-4 py-3">No</th>
                                    <th class="px-4 py-3">Nama Mahasiswa</th>
                                    <th class="px-4 py-3">NIM</th>
                                    <th class="px-4 py-3">Nama Dosen</th>
                                    <th class="rounded-tr-lg px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($bimbingans as $item)
                                    <tr wire:key="bimbingan-{{ $item->id }}"
                                        class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 text-gray-500">
                                            {{ $bimbingans->firstItem() + $loop->index }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-800">
                                            {{ $item->mahasiswa->user->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 font-mono">
                                            {{ $item->mahasiswa->nim ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-700">
                                            {{ $item->dosen->user->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button wire:click="hapus({{ $item->id }})"
                                                wire:confirm="Yakin ingin menghapus penugasan ini?"
                                                wire:loading.attr="disabled" wire:target="hapus({{ $item->id }})"
                                                class="inline-flex items-center gap-1 rounded-md bg-red-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-1 disabled:opacity-60 disabled:cursor-not-allowed transition-colors">
                                                <svg wire:loading wire:target="hapus({{ $item->id }})"
                                                    class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4" />
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8v8z" />
                                                </svg>
                                                <svg wire:loading.remove wire:target="hapus({{ $item->id }})"
                                                    class="h-3 w-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3M3 7h18" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $bimbingans->links('vendor.pagination.tailwind') }}
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>
