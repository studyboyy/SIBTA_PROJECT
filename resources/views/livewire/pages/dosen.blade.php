<div class="space-y-6">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-sky-900 to-emerald-800 px-6 py-7 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-emerald-100/80">Modul Admin</p>
                <h1 class="mt-2 text-2xl font-semibold sm:text-3xl">Data Dosen Pembimbing</h1>
                <p class="mt-2 max-w-2xl text-sm text-emerald-50/90 sm:text-base">
                    Kelola profil dosen pembimbing, kuota bimbingan, serta informasi kontak dalam satu halaman.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-emerald-100/70">Total Data</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $dosenList->total() }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-emerald-100/70">Dipilih</p>
                    <p class="mt-2 text-2xl font-semibold">{{ count($selectedIds) }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-emerald-100/70">Per Halaman</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $dosenList->count() }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Daftar Dosen</h2>
                <p class="text-sm text-slate-500">Tambah, edit, dan hapus data dosen pembimbing tugas akhir.</p>
            </div>
            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
                <div class="relative w-full sm:w-72">
                    <input type="text" wire:model.live.debounce.350ms="search"
                        placeholder="Cari nama, NIDN, atau jabatan..."
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 pl-9 text-sm text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" />
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="pointer-events-none absolute left-3 top-2.5 size-4 text-slate-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6 6a7.5 7.5 0 0 0 10.65 10.65Z" />
                    </svg>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="button" wire:click="confirmDeleteSelected"
                        class="inline-flex items-center rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-500 disabled:cursor-not-allowed disabled:opacity-50"
                        @disabled(empty($selectedIds))>
                        Hapus Terpilih ({{ count($selectedIds) }})
                    </button>
                    <button type="button" wire:click="$dispatch('open-modal', {name: 'dosen'})"
                        class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                        + Tambah Dosen
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <x-ui.show-entries wire:model.live="perPage" class="focus:border-emerald-500 focus:ring-emerald-500" />
        </div>

        <div class="mt-3 overflow-x-auto rounded-2xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">
                            <input type="checkbox" wire:model.live="selectPage"
                                class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" />
                        </th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Dosen</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">NIDN</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Jabatan</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Whatsapp</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Kuota</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($dosenList as $d)
                        <tr wire:key="dosen-{{ $d->id }}" class="hover:bg-slate-50/70">
                            <td class="px-4 py-3 text-slate-600">
                                <input type="checkbox" wire:model.live="selectedIds" value="{{ $d->id }}"
                                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" />
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 overflow-hidden rounded-full bg-slate-100">
                                        @if ($d->user?->photo)
                                            <img src="{{ asset('storage/' . $d->user->photo) }}"
                                                alt="Foto {{ $d->user->name }}" class="size-full object-cover">
                                        @else
                                            <div
                                                class="flex size-full items-center justify-center text-xs font-semibold text-slate-500">
                                                {{ $d->user?->initials() }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $d->user->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $d->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $d->nidn }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $d->jabatan }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $d->phone }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                    {{ $d->kuota_bimbingan }} mahasiswa
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <button wire:click="edit({{ $d->id }})"
                                        class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">
                                        Edit
                                    </button>
                                    <button wire:click="confirmResetPassword({{ $d->id }})"
                                        class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-100">
                                        Reset Password
                                    </button>
                                    <button wire:click="confirmDelete({{ $d->id }})"
                                        class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">
                                Belum ada data dosen.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $dosenList->links('vendor.pagination.tailwind') }}
        </div>
    </section>

    <livewire:components.modal name="dosen">
        <div class="w-full">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">{{ $editId ? 'Edit Data Dosen' : 'Tambah Dosen Baru' }}</h3>
                    <p class="mt-0.5 text-xs text-slate-400">Isi data dosen pembimbing secara lengkap.</p>
                </div>
                <button wire:click="closeModal" type="button"
                    class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="mt-4 border-t border-slate-100"></div>

            <form wire:submit.prevent="store" novalidate class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                @csrf
                @if (! $editId)
                    <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-xs text-emerald-700 sm:col-span-2">
                        Password default akun baru: <span class="font-bold text-emerald-900">Dosen123!</span>
                    </div>
                @endif

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                    <input type="text" wire:model.defer="name" placeholder="Nama dosen"
                        class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                    @error('name') <x-ui.validation-error :message="$message" /> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">NIDN</label>
                    <input type="text" wire:model.defer="nidn" inputmode="numeric"
                        placeholder="Contoh: 1010019001"
                        class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                    @error('nidn') <x-ui.validation-error :message="$message" /> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Jabatan</label>
                    <input type="text" wire:model.defer="jabatan" placeholder="Contoh: Lektor"
                        class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                    @error('jabatan') <x-ui.validation-error :message="$message" /> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Email</label>
                    <input type="email" wire:model.defer="email" placeholder="dosen@email.com"
                        class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                    @error('email') <x-ui.validation-error :message="$message" /> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">No. WhatsApp</label>
                    <input type="text" wire:model.defer="phone" inputmode="numeric"
                        placeholder="08xxxxxxxxxx"
                        class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                    @error('phone') <x-ui.validation-error :message="$message" /> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Kuota Bimbingan</label>
                    <input type="number" min="0" wire:model.defer="kuota_bimbingan" placeholder="Contoh: 10"
                        class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                    @error('kuota_bimbingan') <x-ui.validation-error :message="$message" /> @enderror
                </div>

                <div class="sm:col-span-2 border-t border-slate-100 pt-4 flex justify-end gap-3">
                    <button type="button" wire:click="closeModal"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="rounded-xl bg-emerald-600 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                        {{ $editId ? 'Simpan Perubahan' : 'Tambah Dosen' }}
                    </button>
                </div>
            </form>
        </div>
    </livewire:components.modal>

    <livewire:components.modal name="delete">
        <div class="w-full">
            <div class="flex items-center gap-4">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-rose-100">
                    <svg class="size-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Hapus Data Dosen</h3>
                    <p class="mt-0.5 text-sm text-slate-500">
                        Data <span class="font-semibold text-slate-700">{{ $name }}</span> akan dihapus permanen.
                    </p>
                </div>
            </div>
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" wire:click="$dispatch('close-modal', {name: 'delete'})"
                    class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                <button type="button" wire:click="delete"
                    class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Ya, Hapus</button>
            </div>
        </div>
    </livewire:components.modal>

    <livewire:components.modal name="delete-selected-dosen">
        <div class="w-full">
            <div class="flex items-center gap-4">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-rose-100">
                    <svg class="size-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Hapus Dosen Terpilih</h3>
                    <p class="mt-0.5 text-sm text-slate-500">{{ count($selectedIds) }} data dosen terpilih akan dihapus permanen.</p>
                </div>
            </div>
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" wire:click="$dispatch('close-modal', {name: 'delete-selected-dosen'})"
                    class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                <button type="button" wire:click="deleteSelected"
                    class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Ya, Hapus</button>
            </div>
        </div>
    </livewire:components.modal>

    <livewire:components.modal name="reset-dosen-password">
        <div class="w-full">
            <div class="flex items-center gap-4">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-blue-100">
                    <svg class="size-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a5.25 5.25 0 0 1 5.25 5.25v3.75H18V10.5a3.75 3.75 0 1 0-7.5 0v3.75H9V10.5a5.25 5.25 0 0 1 5.25-5.25h1.5ZM4.5 19.5h15a.75.75 0 0 0 .75-.75V12.75a.75.75 0 0 0-.75-.75h-15a.75.75 0 0 0-.75.75v6a.75.75 0 0 0 .75.75Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Reset Password</h3>
                    <p class="mt-0.5 text-sm text-slate-500">
                        Password untuk <span class="font-semibold text-slate-700">{{ $resetEmail }}</span> akan diubah ke password default <span class="font-bold text-slate-800">Dosen123!</span>.
                    </p>
                </div>
            </div>
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" wire:click="$dispatch('close-modal', {name: 'reset-dosen-password'})"
                    class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                <button type="button" wire:click="resetPassword"
                    class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Ya, Reset</button>
            </div>
        </div>
    </livewire:components.modal>
</div>
