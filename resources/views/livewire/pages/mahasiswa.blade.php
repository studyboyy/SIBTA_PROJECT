<div class="space-y-6">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-blue-900 to-cyan-800 px-6 py-7 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-100/80">Modul Admin</p>
                <h1 class="mt-2 text-2xl font-semibold sm:text-3xl">Data Mahasiswa Tugas Akhir</h1>
                <p class="mt-2 max-w-2xl text-sm text-blue-100 sm:text-base">
                    Kelola akun mahasiswa, status tugas akhir, dan informasi dasar dengan form yang lebih ringkas.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-cyan-100/70">Total Data</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $mahasiswaList->total() }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-cyan-100/70">Dipilih</p>
                    <p class="mt-2 text-2xl font-semibold">{{ count($selectedIds) }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-cyan-100/70">Per Halaman</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $mahasiswaList->count() }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Daftar Mahasiswa</h2>
                <p class="text-sm text-slate-500">Aksi tambah, ubah, dan hapus data mahasiswa tugas akhir.</p>
            </div>
            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
                <div class="relative w-full sm:w-72">
                    <input type="text" wire:model.live.debounce.350ms="search"
                        placeholder="Cari nama, NIM, atau prodi..."
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 pl-9 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500" />
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="pointer-events-none absolute left-3 top-2.5 size-4 text-slate-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6 6a7.5 7.5 0 0 0 10.65 10.65Z" />
                    </svg>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="button" wire:click="deleteSelected"
                        wire:confirm="Yakin ingin menghapus semua data mahasiswa yang dipilih?"
                        class="inline-flex items-center rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-500 disabled:cursor-not-allowed disabled:opacity-50"
                        @disabled(empty($selectedIds))>
                        Hapus Terpilih ({{ count($selectedIds) }})
                    </button>
                    <button type="button" wire:click="$dispatch('open-modal', { name: 'mahasiswa' })"
                        class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500">
                        + Tambah Mahasiswa
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <x-ui.show-entries wire:model.live="perPage" class="focus:border-blue-500 focus:ring-blue-500" />
        </div>

        <div class="mt-3 overflow-x-auto rounded-2xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">
                            <input type="checkbox" wire:model.live="selectPage"
                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" />
                        </th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Mahasiswa</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">NIM</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Program Studi</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Angkatan</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Status TA</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($mahasiswaList as $m)
                        <tr wire:key="mahasiswa-{{ $m->id }}" class="hover:bg-slate-50/70">
                            <td class="px-4 py-3 text-slate-600">
                                <input type="checkbox" wire:model.live="selectedIds" value="{{ $m->id }}"
                                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" />
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 overflow-hidden rounded-full bg-slate-100">
                                        @if ($m->user?->photo)
                                            <img src="{{ asset('storage/' . $m->user->photo) }}"
                                                alt="Foto {{ $m->user->name }}" class="size-full object-cover">
                                        @else
                                            <div
                                                class="flex size-full items-center justify-center text-xs font-semibold text-slate-500">
                                                {{ $m->user?->initials() }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $m->user->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $m->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $m->nim }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $m->prodi }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $m->angkatan }}</td>
                            <td class="px-4 py-3">
                                <span @class([
                                    'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                    'bg-amber-100 text-amber-700' => $m->status_ta === 'Pending',
                                    'bg-blue-100 text-blue-700' => $m->status_ta === 'Proses',
                                    'bg-emerald-100 text-emerald-700' => $m->status_ta === 'Selesai',
                                ])>
                                    {{ $m->status_ta }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <button wire:click="edit({{ $m->id }})"
                                        class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">
                                        Edit
                                    </button>
                                    <button wire:click="confirmResetPassword({{ $m->id }})"
                                        class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-100">
                                        Reset Password
                                    </button>
                                    <button wire:click="confirmDelete({{ $m->id }})"
                                        class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">
                                Belum ada data mahasiswa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $mahasiswaList->links('vendor.pagination.tailwind') }}
        </div>
    </section>

    <livewire:components.modal name="mahasiswa">
        <div class="w-full">
            <h3 class="text-center text-lg font-semibold text-slate-900">
                {{ $editId ? 'Edit Mahasiswa' : 'Tambah Mahasiswa' }}</h3>
            <p class="mt-1 text-center text-sm text-slate-500">Lengkapi data berikut lalu simpan perubahan.</p>

            <form wire:submit.prevent="store" class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                @csrf
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                    <input id="name" type="text" wire:model.defer="name" placeholder="Nama mahasiswa"
                        class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" />
                    @error('name')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div>
                    <label for="nim" class="block text-sm font-medium text-slate-700">NIM</label>
                    <input id="nim" type="text" wire:model.defer="nim" placeholder="Nomor induk mahasiswa"
                        class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" />
                    @error('nim')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div>
                    <label for="angkatan" class="block text-sm font-medium text-slate-700">Angkatan</label>
                    <input id="angkatan" type="text" wire:model.defer="angkatan" placeholder="Contoh: 2023"
                        class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" />
                    @error('angkatan')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                    <input id="email" type="email" wire:model.defer="email" placeholder="nama@email.com"
                        class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" />
                    @error('email')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div>
                    <label for="prodi_id" class="block text-sm font-medium text-slate-700">Program Studi /
                        Jurusan</label>
                    <select id="prodi_id" wire:model.defer="prodi_id"
                        class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih program studi</option>
                        @foreach ($prodiOptions as $prodiOption)
                            <option value="{{ $prodiOption->id }}">{{ $prodiOption->name }}</option>
                        @endforeach
                    </select>
                    @error('prodi_id')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div>
                    <label for="status_ta" class="block text-sm font-medium text-slate-700">Status Tugas Akhir</label>
                    <select id="status_ta" wire:model.defer="status_ta"
                        class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih status TA</option>
                        <option value="Pending">Pending</option>
                        <option value="Proses">Proses</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                    @error('status_ta')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div class="sm:col-span-2 mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                    <button type="button" wire:click="closeModal"
                        class="inline-flex w-full justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="inline-flex w-full justify-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500">
                        {{ $editId ? 'Simpan Perubahan' : 'Simpan Mahasiswa' }}
                    </button>
                </div>
            </form>
        </div>
    </livewire:components.modal>

    <livewire:components.modal name="delete">
        <div class="w-full">
            <h3 class="text-center text-lg font-semibold text-slate-900">Hapus Data Mahasiswa</h3>
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

    <livewire:components.modal name="reset-mahasiswa-password">
        <div class="w-full">
            <h3 class="text-center text-lg font-semibold text-slate-900">Reset password mahasiswa?</h3>
            <p class="mt-2 text-center text-sm text-slate-600">
                Password untuk <span class="font-semibold text-slate-900">{{ $resetEmail }}</span> akan diubah ke
                password acak baru.
            </p>

            <div class="mt-5 grid grid-cols-1 gap-2 sm:grid-cols-2">
                <button type="button" wire:click="$dispatch('close-modal', {name: 'reset-mahasiswa-password'})"
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
