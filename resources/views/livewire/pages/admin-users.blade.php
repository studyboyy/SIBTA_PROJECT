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
            <h3 class="text-center text-base font-semibold text-gray-900">
                {{ $editId ? 'Edit User Admin' : 'Tambah User Admin' }}</h3>

            <form wire:submit.prevent="store" class="mt-6 space-y-5">
                <div>
                    <label for="admin_user_name" class="block text-sm font-medium text-slate-700">Nama lengkap</label>
                    <input id="admin_user_name" type="text" wire:model="name"
                        class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                    @error('name')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div>
                    <label for="admin_user_email" class="block text-sm font-medium text-slate-700">Email</label>
                    <input id="admin_user_email" type="email" wire:model="email"
                        class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                    @error('email')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div>
                    <label for="admin_user_password"
                        class="block text-sm font-medium text-slate-700">{{ $editId ? 'Password baru' : 'Password' }}</label>
                    <input id="admin_user_password" type="password" wire:model="password"
                        class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                    @error('password')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                    @if ($editId)
                        <p class="mt-2 text-xs text-slate-500">Kosongkan jika tidak ingin mengubah password.</p>
                    @endif
                </div>

                <div>
                    <label for="admin_user_password_confirmation"
                        class="block text-sm font-medium text-slate-700">Konfirmasi password</label>
                    <input id="admin_user_password_confirmation" type="password" wire:model="password_confirmation"
                        class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" wire:click="closeModal"
                        class="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                        {{ $editId ? 'Simpan Perubahan' : 'Tambah Admin' }}
                    </button>
                </div>
            </form>
        </div>
    </livewire:components.modal>

    <livewire:components.modal name="delete-admin-user">
        <div class="w-full">
            <h3 class="text-center text-base font-semibold text-gray-900">Hapus user admin {{ $deleteName }}?</h3>
            <p class="mt-3 text-center text-sm text-slate-500">Tindakan ini akan mencabut akses admin dari akun
                tersebut.</p>

            <div class="mt-6 grid grid-cols-2 gap-3">
                <button type="button" wire:click="$dispatch('close-modal', { name: 'delete-admin-user' })"
                    class="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Batal
                </button>
                <button type="button" wire:click="delete"
                    class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500">
                    Hapus
                </button>
            </div>
        </div>
    </livewire:components.modal>

    <livewire:components.modal name="reset-admin-password">
        <div class="w-full">
            <h3 class="text-center text-base font-semibold text-gray-900">Reset password admin?</h3>
            <p class="mt-3 text-center text-sm text-slate-500">
                Password untuk <span class="font-semibold text-slate-700">{{ $resetEmail }}</span> akan diubah ke
                password acak baru.
            </p>

            <div class="mt-6 grid grid-cols-2 gap-3">
                <button type="button" wire:click="$dispatch('close-modal', { name: 'reset-admin-password' })"
                    class="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Batal
                </button>
                <button type="button" wire:click="resetPassword"
                    class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                    Ya, Reset
                </button>
            </div>
        </div>
    </livewire:components.modal>

    <p class="text-xs text-slate-500">Gunakan ikon kunci untuk reset password cepat admin. Password baru akan tampil di
        notifikasi.</p>
</div>
