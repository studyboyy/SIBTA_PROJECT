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
                    <button type="button" wire:click="deleteSelected"
                        wire:confirm="Yakin ingin menghapus semua data dosen yang dipilih?"
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
            <h3 class="text-center text-lg font-semibold text-slate-900">{{ $editId ? 'Edit Dosen' : 'Tambah Dosen' }}
            </h3>
            <p class="mt-1 text-center text-sm text-slate-500">Isi data dosen pembimbing secara lengkap.</p>

            <form wire:submit.prevent="store" class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                @csrf
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                    <input id="name" type="text" wire:model.defer="name" placeholder="Nama dosen"
                        class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                    @error('name')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div>
                    <label for="nidn" class="block text-sm font-medium text-slate-700">NIDN</label>
                    <input id="nidn" type="text" wire:model.defer="nidn" placeholder="Nomor induk dosen"
                        class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                    @error('nidn')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div>
                    <label for="jabatan" class="block text-sm font-medium text-slate-700">Jabatan</label>
                    <input id="jabatan" type="text" wire:model.defer="jabatan" placeholder="Contoh: Lektor"
                        class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                    @error('jabatan')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                    <input id="email" type="email" wire:model.defer="email" placeholder="dosen@email.com"
                        class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                    @error('email')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-700">No Whatsapp</label>
                    <input id="phone" type="text" wire:model.defer="phone" placeholder="08xxxxxxxxxx"
                        class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                    @error('phone')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div>
                    <label for="kuota_bimbingan" class="block text-sm font-medium text-slate-700">Kuota
                        Bimbingan</label>
                    <input id="kuota_bimbingan" type="number" min="0" wire:model.defer="kuota_bimbingan"
                        placeholder="Contoh: 10"
                        class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                    @error('kuota_bimbingan')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div class="sm:col-span-2 mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                    <button type="button" wire:click="closeModal"
                        class="inline-flex w-full justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="inline-flex w-full justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                        {{ $editId ? 'Simpan Perubahan' : 'Simpan Dosen' }}
                    </button>
                </div>
            </form>
        </div>
    </livewire:components.modal>

    <livewire:components.modal name="delete">
        <div class="w-full">
            <h3 class="text-center text-lg font-semibold text-slate-900">Hapus Data Dosen</h3>
            <p class="mt-2 text-center text-sm text-slate-600">
                Data <span class="font-semibold text-slate-900">{{ $name }}</span> akan dihapus permanen.
            </p>
            <p class="mt-1 text-center text-xs text-rose-600">Tindakan ini tidak dapat dibatalkan.</p>

            <div class="mt-5 grid grid-cols-1 gap-2 sm:grid-cols-2">
                <button type="button" wire:click="$dispatch('close-modal', {name: 'delete'})"
                    class="inline-flex w-full justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Batal
                </button>
                <button type="button" wire:click="delete"
                    class="inline-flex w-full justify-center rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-500">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </livewire:components.modal>

    <livewire:components.modal name="reset-dosen-password">
        <div class="w-full">
            <h3 class="text-center text-lg font-semibold text-slate-900">Reset password dosen?</h3>
            <p class="mt-2 text-center text-sm text-slate-600">
                Password untuk <span class="font-semibold text-slate-900">{{ $resetEmail }}</span> akan diubah ke
                password acak baru.
            </p>

            <div class="mt-5 grid grid-cols-1 gap-2 sm:grid-cols-2">
                <button type="button" wire:click="$dispatch('close-modal', {name: 'reset-dosen-password'})"
                    class="inline-flex w-full justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Batal
                </button>
                <button type="button" wire:click="resetPassword"
                    class="inline-flex w-full justify-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500">
                    Ya, Reset
                </button>
            </div>
        </div>
    </livewire:components.modal>
</div>
