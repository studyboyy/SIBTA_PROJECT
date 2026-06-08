<div class="space-y-8">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-slate-800 to-blue-900 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <p class="text-sm font-medium uppercase tracking-[0.3em] text-blue-100/80">Manajemen Akses</p>
                <h1 class="text-3xl font-semibold sm:text-4xl">User Admin</h1>
                <p class="max-w-2xl text-sm text-blue-100 sm:text-base">
                    Tambah, ubah, dan hapus akun admin yang boleh mengakses seluruh modul administratif sistem.
                </p>
            </div>

            <button type="button" wire:click="$dispatch('open-modal', { name: 'admin-user' })"
                class="rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-900 shadow hover:bg-slate-100">
                Tambah User Admin
            </button>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Daftar User Admin</h2>
                <p class="text-sm text-slate-500">Gunakan pencarian untuk menemukan akun admin tertentu.</p>
            </div>
            <div class="w-full lg:max-w-sm">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau email admin"
                    class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
            </div>
        </div>

        <div class="mt-6">
            <x-ui.show-entries wire:model.live="perPage" class="focus:border-blue-500 focus:ring-blue-500" />
        </div>

        <div class="mt-3 overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr class="text-left text-sm font-semibold text-slate-700">
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Dibuat</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-sm text-slate-600">
                    @forelse ($adminUsers as $admin)
                        <tr>
                            <td class="px-4 py-4 font-medium text-slate-900">{{ $admin->name }}</td>
                            <td class="px-4 py-4">{{ $admin->email }}</td>
                            <td class="px-4 py-4">
                                <span
                                    class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">Admin</span>
                            </td>
                            <td class="px-4 py-4">{{ $admin->created_at?->translatedFormat('d M Y') ?? '-' }}</td>
                            <td class="px-4 py-4">
                                <div class="flex gap-2">
                                    <button wire:click="edit({{ $admin->id }})"
                                        class="rounded-full border border-amber-200 bg-amber-50 p-2 text-amber-700">
                                        <flux:icon.pencil variant="solid" class="size-3" />
                                    </button>
                                    <button wire:click="confirmResetPassword({{ $admin->id }})"
                                        class="rounded-full border border-blue-200 bg-blue-50 p-2 text-blue-700"
                                        title="Reset password cepat">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor" class="size-3">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 5.25a5.25 5.25 0 0 1 4.243 8.343l-1.13 1.13a.75.75 0 0 1-.53.22h-1.72v1.72a.75.75 0 0 1-.22.53l-.97.97a.75.75 0 0 1-.53.22h-1.72v1.72a.75.75 0 0 1-.22.53l-.72.72a.75.75 0 0 1-1.06 0l-3.375-3.375a.75.75 0 0 1 0-1.06l4.74-4.74A5.25 5.25 0 1 1 15.75 5.25Z" />
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $admin->id }})"
                                        class="rounded-full border border-red-200 bg-red-50 p-2 text-red-700">
                                        <flux:icon.trash variant="solid" class="size-3" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada user admin
                                yang sesuai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $adminUsers->links('vendor.pagination.tailwind') }}
        </div>
    </section>

    <livewire:components.modal name="admin-user">
        <div class="w-full">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">{{ $editId ? 'Edit User Admin' : 'Tambah User Admin' }}</h3>
                    <p class="mt-0.5 text-xs text-slate-400">Lengkapi data akun admin.</p>
                </div>
                <button wire:click="closeModal" type="button"
                    class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mt-4 border-t border-slate-100"></div>

            <form wire:submit.prevent="store" novalidate class="mt-4 space-y-4">
                @if (! $editId)
                    <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-xs text-blue-700">
                        Akun admin tidak memakai password default. Isi password awal minimal 8 karakter.
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                    <input type="text" wire:model="name" placeholder="Nama admin"
                        class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                    @error('name') <x-ui.validation-error :message="$message" /> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Email</label>
                    <input type="email" wire:model="email" placeholder="admin@email.com"
                        class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                    @error('email') <x-ui.validation-error :message="$message" /> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">{{ $editId ? 'Password Baru' : 'Password' }}</label>
                    <input type="password" wire:model="password" placeholder="Minimal 8 karakter"
                        class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                    @error('password') <x-ui.validation-error :message="$message" /> @enderror
                    @if ($editId) <p class="mt-1 text-xs text-slate-400">Kosongkan jika tidak ingin mengubah password.</p> @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Konfirmasi Password</label>
                    <input type="password" wire:model="password_confirmation" placeholder="Ulangi password"
                        class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                </div>
                <div class="border-t border-slate-100 pt-4 flex justify-end gap-3">
                    <button type="button" wire:click="closeModal"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit"
                        class="rounded-xl bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        {{ $editId ? 'Simpan Perubahan' : 'Tambah Admin' }}
                    </button>
                </div>
            </form>
        </div>
    </livewire:components.modal>

    <livewire:components.modal name="delete-admin-user">
        <div class="w-full">
            <div class="flex items-center gap-4">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-rose-100">
                    <svg class="size-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Hapus User Admin</h3>
                    <p class="mt-0.5 text-sm text-slate-500">Akun <span class="font-semibold text-slate-700">{{ $deleteName }}</span> akan kehilangan akses sistem.</p>
                </div>
            </div>
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" wire:click="$dispatch('close-modal', { name: 'delete-admin-user' })"
                    class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                <button type="button" wire:click="delete"
                    class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Ya, Hapus</button>
            </div>
        </div>
    </livewire:components.modal>

    <livewire:components.modal name="reset-admin-password">
        <div class="w-full">
            <div class="flex items-center gap-4">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-blue-100">
                    <svg class="size-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a5.25 5.25 0 0 1 5.25 5.25v3.75H18V10.5a3.75 3.75 0 1 0-7.5 0v3.75H9V10.5a5.25 5.25 0 0 1 5.25-5.25h1.5ZM4.5 19.5h15a.75.75 0 0 0 .75-.75V12.75a.75.75 0 0 0-.75-.75h-15a.75.75 0 0 0-.75.75v6a.75.75 0 0 0 .75.75Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Reset Password Admin</h3>
                    <p class="mt-0.5 text-sm text-slate-500">Password untuk <span class="font-semibold text-slate-700">{{ $resetEmail }}</span> akan diubah ke password acak baru.</p>
                </div>
            </div>
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" wire:click="$dispatch('close-modal', { name: 'reset-admin-password' })"
                    class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                <button type="button" wire:click="resetPassword"
                    class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Ya, Reset</button>
            </div>
        </div>
    </livewire:components.modal>

    <p class="text-xs text-slate-400">Gunakan ikon kunci untuk reset password cepat admin. Password baru akan tampil di notifikasi.</p>
</div>
