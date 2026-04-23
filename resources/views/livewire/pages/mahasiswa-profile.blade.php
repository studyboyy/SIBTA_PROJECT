<div class="space-y-8">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-emerald-900 to-teal-700 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <p class="text-sm font-medium uppercase tracking-[0.3em] text-emerald-100/80">Pengaturan Akun</p>
                <h1 class="text-3xl font-semibold sm:text-4xl">Profil Mahasiswa</h1>
                <p class="max-w-2xl text-sm text-emerald-50/90 sm:text-base">
                    Kelola identitas akun mahasiswa yang dipakai untuk pengajuan judul, bimbingan, dan dokumen tugas
                    akhir.
                </p>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/10 p-5 backdrop-blur">
                <p class="text-xs uppercase tracking-[0.25em] text-emerald-100/70">Identitas Akademik</p>
                <div class="mt-3 space-y-2 text-sm text-emerald-50">
                    <p>NIM: {{ $mahasiswa?->nim ?? '-' }}</p>
                    <p>Program Studi: {{ $mahasiswa?->prodi ?? '-' }}</p>
                    <p>Angkatan: {{ $mahasiswa?->angkatan ?? '-' }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-[1fr_0.9fr]">
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Informasi Profil</h2>
                <p class="text-sm text-slate-500">Nama dan email ini dipakai sebagai identitas utama akun mahasiswa.</p>
            </div>

            <form wire:submit="saveProfile" class="mt-6 space-y-5">
                <div>
                    <label for="mahasiswa_photo" class="block text-sm font-medium text-slate-700">Foto profil</label>
                    <div class="mt-3 flex items-center gap-4">
                        @if ($photo)
                            <img src="{{ $photo->temporaryUrl() }}" alt="Preview foto"
                                class="size-16 rounded-full object-cover ring-2 ring-emerald-100" />
                        @elseif ($oldPhoto)
                            <img src="{{ Storage::url($oldPhoto) }}" alt="Foto mahasiswa"
                                class="size-16 rounded-full object-cover ring-2 ring-emerald-100" />
                        @else
                            <div
                                class="flex size-16 items-center justify-center rounded-full bg-emerald-100 text-lg font-semibold text-emerald-700 ring-2 ring-emerald-100">
                                {{ Auth::user()?->initials() }}
                            </div>
                        @endif
                        <input id="mahasiswa_photo" type="file" wire:model="photo" accept="image/*"
                            class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                    </div>
                    @error('photo')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div>
                    <label for="mahasiswa_name" class="block text-sm font-medium text-slate-700">Nama lengkap</label>
                    <input id="mahasiswa_name" type="text" wire:model="name"
                        class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                    @error('name')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div>
                    <label for="mahasiswa_email" class="block text-sm font-medium text-slate-700">Email</label>
                    <input id="mahasiswa_email" type="email" wire:model="email"
                        class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                    @error('email')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-500">
                        Simpan Profil
                    </button>
                </div>
            </form>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Keamanan Akun</h2>
                <p class="text-sm text-slate-500">Perbarui password secara berkala untuk menjaga keamanan akun
                    mahasiswa.</p>
            </div>

            <form wire:submit="updatePassword" class="mt-6 space-y-5">
                <div>
                    <label for="mahasiswa_current_password" class="block text-sm font-medium text-slate-700">Password
                        saat ini</label>
                    <input id="mahasiswa_current_password" type="password" wire:model="current_password"
                        class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                    @error('current_password')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div>
                    <label for="mahasiswa_password" class="block text-sm font-medium text-slate-700">Password
                        baru</label>
                    <input id="mahasiswa_password" type="password" wire:model="password"
                        class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                    @error('password')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div>
                    <label for="mahasiswa_password_confirmation"
                        class="block text-sm font-medium text-slate-700">Konfirmasi password baru</label>
                    <input id="mahasiswa_password_confirmation" type="password" wire:model="password_confirmation"
                        class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
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
