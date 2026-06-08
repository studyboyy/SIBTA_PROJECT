<div class="space-y-8">

    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-blue-900 to-indigo-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-indigo-100/80">Panel Admin</p>
            <h1 class="text-3xl font-semibold sm:text-4xl">Penugasan Bimbingan</h1>
            <p class="max-w-2xl text-sm text-indigo-100 sm:text-base">
                Tetapkan dosen pembimbing untuk mahasiswa. Rekomendasi calon dosen dari pengajuan judul mahasiswa
                ditampilkan sebagai referensi.
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

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- FORM PENUGASAN --}}
        <div class="lg:col-span-1">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-slate-900">Tambah Penugasan</h2>
                <p class="mt-1 text-sm text-slate-500">Pilih mahasiswa dan tetapkan dosen pembimbingnya.</p>

                <div class="mt-5 space-y-4">

                    {{-- Multi-select Mahasiswa --}}
                    <div x-data="{ open: false, search: '' }">
                        <label class="mb-1 block text-sm font-medium text-slate-700">Mahasiswa</label>
                        <p class="mb-2 text-xs text-slate-400">Mahasiswa yang sudah punya pembimbing tidak muncul di sini.</p>

                        <button type="button" @click="open = !open"
                            class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 hover:bg-slate-50 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <span>
                                @if (count($mahasiswa_ids) > 0)
                                    <span class="font-medium text-blue-600">{{ count($mahasiswa_ids) }} mahasiswa dipilih</span>
                                @else
                                    <span class="text-slate-400">-- Pilih Mahasiswa --</span>
                                @endif
                            </span>
                            <svg :class="open ? 'rotate-180' : ''" class="h-4 w-4 text-slate-400 transition-transform"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition @click.outside="open = false"
                            class="relative z-10 mt-1 w-full rounded-xl border border-slate-200 bg-white shadow-lg">
                            <div class="border-b border-slate-100 p-2">
                                <input type="text" x-model="search" placeholder="Cari nama / NIM..."
                                    class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-700 focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-300" />
                            </div>

                            <div class="max-h-52 overflow-y-auto">
                                @if ($mahasiswas->isEmpty())
                                    <p class="px-3 py-4 text-center text-xs text-slate-400">Semua mahasiswa sudah memiliki dosen pembimbing.</p>
                                @else
                                    @foreach ($mahasiswas as $mhs)
                                        <label
                                            x-show="'{{ strtolower($mhs->user->name) }} {{ strtolower($mhs->nim) }}'.includes(search.toLowerCase())"
                                            class="flex cursor-pointer items-center gap-3 px-3 py-2 transition-colors hover:bg-blue-50">
                                            <input type="checkbox" wire:model="mahasiswa_ids" value="{{ $mhs->id }}"
                                                class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500" />
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-sm font-medium text-slate-800">{{ $mhs->user->name }}</p>
                                                <p class="font-mono text-xs text-slate-400">{{ $mhs->nim }}</p>
                                            </div>
                                        </label>
                                    @endforeach
                                @endif
                            </div>

                            <div class="flex items-center justify-between border-t border-slate-100 px-3 py-2">
                                <span class="text-xs text-slate-400">{{ count($mahasiswas) }} tersedia</span>
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
                        <label class="mb-1 block text-sm font-medium text-slate-700">Dosen Pembimbing</label>
                        <p class="mb-2 text-xs text-slate-400">Dosen yang kuota penuh tidak dapat dipilih.</p>
                        <select wire:model="dosen_id"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <option value="">-- Pilih Dosen --</option>
                            @foreach ($dosens as $dsn)
                                <option value="{{ $dsn['id'] }}" @disabled($dsn['is_full'])>
                                    {{ $dsn['name'] }} ({{ $dsn['nidn'] }}) — Sisa {{ $dsn['sisa'] }}/{{ $dsn['kuota'] }}
                                </option>
                            @endforeach
                        </select>

                        @error('dosen_id')
                            <x-ui.validation-error :message="$message" />
                        @enderror
                    </div>

                    <button wire:click="simpan" wire:loading.attr="disabled"
                        class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <svg wire:loading wire:target="simpan" class="h-4 w-4 animate-spin"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                        </svg>
                        <span wire:loading.remove wire:target="simpan">Tambah Penugasan</span>
                        <span wire:loading wire:target="simpan">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- TABLE LIST BIMBINGAN --}}
        <div class="lg:col-span-2">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Data Penugasan</h2>
                        <p class="mt-1 text-sm text-slate-500">Daftar mahasiswa beserta dosen pembimbing yang ditetapkan.</p>
                    </div>
                    <div class="relative w-full sm:w-64">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
                            </svg>
                        </span>
                        <input type="text" wire:model.live.debounce.400ms="search"
                            placeholder="Cari mahasiswa, NIM, atau dosen..."
                            class="w-full rounded-xl border border-slate-200 py-2 pl-9 pr-3 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                    </div>
                </div>

                @if ($bimbingans->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                        <svg class="mb-3 h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                        <p class="text-sm font-medium">Belum ada penugasan bimbingan</p>
                        <p class="mt-1 text-xs text-slate-400">Tambahkan penugasan melalui form di sebelah kiri.</p>
                    </div>
                @else
                    <div class="mb-4">
                        <x-ui.show-entries wire:model.live="perPage" class="focus:border-blue-500 focus:ring-blue-500" />
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse text-sm">
                            <thead>
                                <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                    <th class="rounded-tl-xl px-4 py-3">No</th>
                                    <th class="px-4 py-3">Nama Mahasiswa</th>
                                    <th class="px-4 py-3">NIM</th>
                                    <th class="px-4 py-3">Dosen Pembimbing</th>
                                    <th class="rounded-tr-xl px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($bimbingans as $item)
                                    <tr wire:key="bimbingan-{{ $item->id }}" class="transition-colors hover:bg-slate-50">
                                        <td class="px-4 py-3 text-slate-500">
                                            {{ $bimbingans->firstItem() + $loop->index }}
                                        </td>
                                        <td class="px-4 py-3 font-medium text-slate-800">
                                            {{ $item->mahasiswa->user->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 font-mono text-slate-600">
                                            {{ $item->mahasiswa->nim ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-slate-700">
                                            {{ $item->dosen->user->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button wire:click="confirmHapus({{ $item->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="confirmHapus({{ $item->id }})"
                                                class="inline-flex items-center gap-1.5 rounded-xl bg-red-500 px-3 py-1.5 text-xs font-medium text-white transition-colors hover:bg-red-600 disabled:cursor-not-allowed disabled:opacity-60">
                                                <svg wire:loading wire:target="confirmHapus({{ $item->id }})"
                                                    class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4" />
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                                                </svg>
                                                <svg wire:loading.remove wire:target="confirmHapus({{ $item->id }})"
                                                    class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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

    <livewire:components.modal name="delete-bimbingan">
        <div class="w-full">
            <div class="flex items-center gap-4">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-rose-100">
                    <svg class="size-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Hapus Penugasan</h3>
                    <p class="mt-0.5 text-sm text-slate-500">
                        Penugasan <span class="font-semibold text-slate-700">{{ $deleteName }}</span> akan dihapus.
                    </p>
                </div>
            </div>
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" wire:click="$dispatch('close-modal', {name: 'delete-bimbingan'})"
                    class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Batal
                </button>
                <button type="button" wire:click="hapusConfirmed"
                    class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </livewire:components.modal>
</div>
