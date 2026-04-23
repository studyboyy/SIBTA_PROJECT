<div class="space-y-6">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-sky-900 to-indigo-800 px-6 py-7 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-100/80">Modul Admin</p>
                <h1 class="mt-2 text-2xl font-semibold sm:text-3xl">Kelola Kaprodi</h1>
                <p class="mt-2 max-w-2xl text-sm text-blue-100 sm:text-base">Atur akun kaprodi dan kaitkan setiap user ke
                    satu program studi yang diampu.</p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-blue-100/70">Total Kaprodi</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $kaprodiList->total() }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-blue-100/70">Tampil</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $kaprodiList->count() }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Akun Kaprodi</h2>
                <p class="text-sm text-slate-500">Tambah, edit, reset password, dan tetapkan prodi yang diampu.</p>
            </div>
            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
                <div class="relative w-full sm:w-72">
                    <input type="text" wire:model.live.debounce.350ms="search"
                        placeholder="Cari nama, email, atau prodi..."
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 pl-9 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500" />
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="pointer-events-none absolute left-3 top-2.5 size-4 text-slate-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6 6a7.5 7.5 0 0 0 10.65 10.65Z" />
                    </svg>
                </div>
                <button type="button" wire:click="$dispatch('open-modal', {name: 'kaprodi'})"
                    class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500">+
                    Tambah Kaprodi</button>
            </div>
        </div>

        <div class="mt-5"><x-ui.show-entries wire:model.live="perPage"
                class="focus:border-blue-500 focus:ring-blue-500" /></div>

        <div class="mt-3 overflow-x-auto rounded-2xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Kaprodi</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Email</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Program Studi</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($kaprodiList as $kaprodi)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $kaprodi->name }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $kaprodi->email }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $kaprodi->managedProdi?->name ?? 'Belum ditetapkan' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <button wire:click="edit({{ $kaprodi->id }})"
                                        class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">Edit</button>
                                    <button wire:click="confirmResetPassword({{ $kaprodi->id }})"
                                        class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-100">Reset
                                        Password</button>
                                    <button wire:click="confirmDelete({{ $kaprodi->id }})"
                                        class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada akun
                                kaprodi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $kaprodiList->links('vendor.pagination.tailwind') }}</div>
    </section>

    <livewire:components.modal name="kaprodi">
        <div class="w-full">
            <h3 class="text-center text-lg font-semibold text-slate-900">
                {{ $editId ? 'Edit Kaprodi' : 'Tambah Kaprodi' }}</h3>
            <form wire:submit.prevent="store" class="mt-5 grid grid-cols-1 gap-4">
                <div><label class="block text-sm font-medium text-slate-700">Nama Lengkap</label><input type="text"
                        wire:model.defer="name"
                        class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" />
                    @error('name')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>
                <div><label class="block text-sm font-medium text-slate-700">Email</label><input type="email"
                        wire:model.defer="email"
                        class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" />
                    @error('email')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>
                <div><label class="block text-sm font-medium text-slate-700">Program Studi yang Diampu</label><select
                        wire:model.defer="prodi_id"
                        class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Belum ditetapkan</option>
                        @foreach ($availableProdis as $prodi)
                            <option value="{{ $prodi->id }}">{{ $prodi->name }}</option>
                        @endforeach
                    </select>
                    @error('prodi_id')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3 text-xs text-slate-600">Password default akun kaprodi
                    baru: <span class="font-semibold text-slate-900">Kaprodi123!</span></div>
                <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                    <button type="button" wire:click="closeModal"
                        class="inline-flex w-full justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</button>
                    <button type="submit"
                        class="inline-flex w-full justify-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500">{{ $editId ? 'Simpan Perubahan' : 'Simpan Kaprodi' }}</button>
                </div>
            </form>
        </div>
    </livewire:components.modal>

    <livewire:components.modal name="delete-kaprodi">
        <div class="w-full">
            <h3 class="text-center text-lg font-semibold text-slate-900">Hapus Kaprodi</h3>
            <p class="mt-2 text-center text-sm text-slate-600">Akun <span
                    class="font-semibold text-slate-900">{{ $deleteName }}</span> akan dihapus.</p>
            <div class="mt-5 grid grid-cols-1 gap-2 sm:grid-cols-2"><button type="button"
                    wire:click="$dispatch('close-modal', {name: 'delete-kaprodi'})"
                    class="inline-flex w-full justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</button><button
                    type="button" wire:click="delete"
                    class="inline-flex w-full justify-center rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-500">Ya,
                    Hapus</button></div>
        </div>
    </livewire:components.modal>

    <livewire:components.modal name="reset-kaprodi-password">
        <div class="w-full">
            <h3 class="text-center text-lg font-semibold text-slate-900">Reset password kaprodi?</h3>
            <p class="mt-2 text-center text-sm text-slate-600">Password untuk <span
                    class="font-semibold text-slate-900">{{ $resetEmail }}</span> akan diubah ke <span
                    class="font-semibold text-slate-900">Kaprodi123!</span>.</p>
            <div class="mt-5 grid grid-cols-1 gap-2 sm:grid-cols-2"><button type="button"
                    wire:click="$dispatch('close-modal', {name: 'reset-kaprodi-password'})"
                    class="inline-flex w-full justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</button><button
                    type="button" wire:click="resetPassword"
                    class="inline-flex w-full justify-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500">Ya,
                    Reset</button></div>
        </div>
    </livewire:components.modal>
</div>
