<div class="space-y-8">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-blue-900 to-cyan-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-cyan-100/80">Pengaturan Akun</p>
            <h1 class="text-3xl font-semibold sm:text-4xl">Profil Admin</h1>
            <p class="max-w-2xl text-sm text-blue-100 sm:text-base">
                Kelola informasi akun admin yang sedang login dan perbarui password untuk keamanan akses sistem.
            </p>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-[1fr_0.9fr]">
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Informasi Profil</h2>
                <p class="text-sm text-slate-500">Nama dan email ini dipakai sebagai identitas login admin.</p>
            </div>

            <form wire:submit="saveProfile" novalidate class="mt-6 space-y-5">
                <div>
                    <label for="admin_photo" class="block text-sm font-medium text-slate-700">Foto profil</label>
                    <div class="mt-3 flex items-center gap-4">
                        @if ($photo)
                            <img src="{{ $photo->temporaryUrl() }}" alt="Preview foto"
                                class="size-16 rounded-full object-cover ring-2 ring-blue-100" />
                        @elseif ($oldPhoto)
                            <img src="{{ Storage::url($oldPhoto) }}" alt="Foto admin"
                                class="size-16 rounded-full object-cover ring-2 ring-blue-100" />
                        @else
                            <div
                                class="flex size-16 items-center justify-center rounded-full bg-blue-100 text-lg font-semibold text-blue-700 ring-2 ring-blue-100">
                                {{ Auth::user()?->initials() }}
                            </div>
                        @endif
                        <input id="admin_photo" type="file" wire:model="photo" accept="image/*"
                            class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                    </div>
                    @error('photo')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div>
                    <label for="admin_name" class="block text-sm font-medium text-slate-700">Nama lengkap</label>
                    <input id="admin_name" type="text" wire:model="name" placeholder="Nama lengkap admin"
                        class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                    @error('name')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div>
                    <label for="admin_email" class="block text-sm font-medium text-slate-700">Email</label>
                    <input id="admin_email" type="email" wire:model="email" placeholder="admin@email.com"
                        class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                    @error('email')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-500">
                        Simpan Profil
                    </button>
                </div>
            </form>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Keamanan Akun</h2>
                <p class="text-sm text-slate-500">Gunakan password yang kuat untuk membatasi akses admin.</p>
            </div>

            <form wire:submit="updatePassword" novalidate class="mt-6 space-y-5">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-slate-700">Password saat
                        ini</label>
                    <input id="current_password" type="password" wire:model="current_password" placeholder="Password saat ini"
                        class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                    @error('current_password')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">Password baru</label>
                    <input id="password" type="password" wire:model="password" placeholder="Password baru"
                        class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                    @error('password')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi
                        password baru</label>
                    <input id="password_confirmation" type="password" wire:model="password_confirmation" placeholder="Ulangi password baru"
                        class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                        Perbarui Password
                    </button>
                </div>
            </form>
        </article>
    </section>
</div>
