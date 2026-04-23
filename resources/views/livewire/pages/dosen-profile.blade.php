<div class="space-y-8">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-cyan-900 to-sky-700 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <p class="text-sm font-medium uppercase tracking-[0.3em] text-cyan-100/80">Pengaturan Akun</p>
                <h1 class="text-3xl font-semibold sm:text-4xl">Profil Dosen</h1>
                <p class="max-w-2xl text-sm text-cyan-50/90 sm:text-base">
                    Perbarui identitas akun dosen yang digunakan untuk akses dashboard, bimbingan, dan review tugas
                    akhir.
                </p>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/10 p-5 backdrop-blur">
                <p class="text-xs uppercase tracking-[0.25em] text-cyan-100/70">Identitas Akademik</p>
                <div class="mt-3 space-y-2 text-sm text-cyan-50">
                    <p>NIDN: {{ $dosen?->nidn ?? '-' }}</p>
                    <p>Jabatan: {{ $dosen?->jabatan ?? '-' }}</p>
                    <p>Telepon: {{ $dosen?->phone ?? '-' }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-[1fr_0.9fr]">
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Informasi Profil</h2>
                <p class="text-sm text-slate-500">Nama dan email ini dipakai sebagai identitas utama akun dosen.</p>
            </div>

            <form wire:submit="saveProfile" class="mt-6 space-y-5">
                <div>
                    <label for="dosen_photo" class="block text-sm font-medium text-slate-700">Foto profil</label>
                    <div class="mt-3 flex items-center gap-4">
                        @if ($photo)
                            <img src="{{ $photo->temporaryUrl() }}" alt="Preview foto"
                                class="size-16 rounded-full object-cover ring-2 ring-cyan-100" />
                        @elseif ($oldPhoto)
                            <img src="{{ Storage::url($oldPhoto) }}" alt="Foto dosen"
                                class="size-16 rounded-full object-cover ring-2 ring-cyan-100" />
                        @else
                            <div
                                class="flex size-16 items-center justify-center rounded-full bg-cyan-100 text-lg font-semibold text-cyan-700 ring-2 ring-cyan-100">
                                {{ Auth::user()?->initials() }}
                            </div>
                        @endif
                        <input id="dosen_photo" type="file" wire:model="photo" accept="image/*"
                            class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-100" />
                    </div>
                    @error('photo')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div>
                    <label for="dosen_name" class="block text-sm font-medium text-slate-700">Nama lengkap</label>
                    <input id="dosen_name" type="text" wire:model="name"
                        class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-100" />
                    @error('name')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div>
                    <label for="dosen_email" class="block text-sm font-medium text-slate-700">Email</label>
                    <input id="dosen_email" type="email" wire:model="email"
                        class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-100" />
                    @error('email')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="rounded-2xl bg-cyan-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-cyan-500">
                        Simpan Profil
                    </button>
                </div>
            </form>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Keamanan Akun</h2>
                <p class="text-sm text-slate-500">Gunakan password yang kuat agar akses halaman dosen tetap aman.</p>
            </div>

            <form wire:submit="updatePassword" class="mt-6 space-y-5">
                <div>
                    <label for="dosen_current_password" class="block text-sm font-medium text-slate-700">Password saat
                        ini</label>
                    <input id="dosen_current_password" type="password" wire:model="current_password"
                        class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-100" />
                    @error('current_password')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div>
                    <label for="dosen_password" class="block text-sm font-medium text-slate-700">Password baru</label>
                    <input id="dosen_password" type="password" wire:model="password"
                        class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-100" />
                    @error('password')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div>
                    <label for="dosen_password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi
                        password baru</label>
                    <input id="dosen_password_confirmation" type="password" wire:model="password_confirmation"
                        class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-100" />
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Perbarui Password
                    </button>
                </div>
            </form>
        </article>
    </section>
</div>
