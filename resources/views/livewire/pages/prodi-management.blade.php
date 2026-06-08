<div class="space-y-6">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-teal-900 to-emerald-800 px-6 py-7 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-emerald-100/80">Modul Admin</p>
                <h1 class="mt-2 text-2xl font-semibold sm:text-3xl">Kelola Program Studi</h1>
                <p class="mt-2 max-w-2xl text-sm text-emerald-50/90 sm:text-base">Atur daftar jurusan atau prodi beserta
                    penanggung jawab kaprodinya.</p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-emerald-100/70">Total Prodi</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $prodiList->total() }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-emerald-100/70">Tampil</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $prodiList->count() }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Master Program Studi</h2>
                <p class="text-sm text-slate-500">Daftarkan prodi dan kaitkan dengan satu user kaprodi.</p>
            </div>
            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
                <div class="relative w-full sm:w-72">
                    <input type="text" wire:model.live.debounce.350ms="search"
                        placeholder="Cari nama, kode, atau kaprodi..."
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 pl-9 text-sm text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" />
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="pointer-events-none absolute left-3 top-2.5 size-4 text-slate-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6 6a7.5 7.5 0 0 0 10.65 10.65Z" />
                    </svg>
                </div>
                <button type="button" wire:click="$dispatch('open-modal', {name: 'prodi'})"
                    class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">+
                    Tambah Prodi</button>
            </div>
        </div>

        <div class="mt-5">
            <x-ui.show-entries wire:model.live="perPage" class="focus:border-emerald-500 focus:ring-emerald-500" />
        </div>

        <div class="mt-3 overflow-x-auto rounded-2xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Prodi</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Kode</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Kaprodi</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Mahasiswa</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($prodiList as $prodi)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $prodi->name }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $prodi->code }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $prodi->kaprodiUser?->name ?? 'Belum ditentukan' }}
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $prodi->mahasiswas->count() }} mahasiswa</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <button wire:click="edit({{ $prodi->id }})"
                                        class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">Edit</button>
                                    <button wire:click="confirmDelete({{ $prodi->id }})"
                                        class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada program
                                studi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $prodiList->links('vendor.pagination.tailwind') }}</div>
    </section>

    <livewire:components.modal name="prodi">
        <div class="w-full">
            <h3 class="text-center text-lg font-semibold text-slate-900">
                {{ $editId ? 'Edit Program Studi' : 'Tambah Program Studi' }}</h3>
            <form wire:submit.prevent="store" novalidate class="mt-5 grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Nama Prodi</label>
                    <input type="text" wire:model.defer="name" placeholder="Contoh: Teknik Informatika"
                        class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                    @error('name')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Kode</label>
                    <input type="text" wire:model.defer="code" placeholder="Contoh: TI"
                        class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm uppercase text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                    @error('code')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Kaprodi</label>
                    <select wire:model.defer="kaprodi_user_id"
                        class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                        <option value="">Belum ditentukan</option>
                        @foreach ($kaprodiUsers as $kaprodi)
                            <option value="{{ $kaprodi->id }}">{{ $kaprodi->name }}</option>
                        @endforeach
                    </select>
                    @error('kaprodi_user_id')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>
                <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                    <button type="button" wire:click="closeModal"
                        class="inline-flex w-full justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</button>
                    <button type="submit"
                        class="inline-flex w-full justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">{{ $editId ? 'Simpan Perubahan' : 'Simpan Prodi' }}</button>
                </div>
            </form>
        </div>
    </livewire:components.modal>

    <livewire:components.modal name="delete-prodi">
        <div class="w-full">
            <h3 class="text-center text-lg font-semibold text-slate-900">Hapus Program Studi</h3>
            <p class="mt-2 text-center text-sm text-slate-600">Program studi <span
                    class="font-semibold text-slate-900">{{ $deleteName }}</span> akan dihapus.</p>
            <div class="mt-5 grid grid-cols-1 gap-2 sm:grid-cols-2">
                <button type="button" wire:click="$dispatch('close-modal', {name: 'delete-prodi'})"
                    class="inline-flex w-full justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</button>
                <button type="button" wire:click="delete"
                    class="inline-flex w-full justify-center rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-500">Ya,
                    Hapus</button>
            </div>
        </div>
    </livewire:components.modal>
</div>
