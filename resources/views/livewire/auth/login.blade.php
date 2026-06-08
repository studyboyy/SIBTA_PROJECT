<div
    class="min-h-screen bg-[radial-gradient(circle_at_12%_20%,rgba(14,165,233,0.14),transparent_36%),radial-gradient(circle_at_88%_82%,rgba(16,185,129,0.12),transparent_40%),linear-gradient(160deg,#f8fafc_0%,#f1f5f9_45%,#ecfeff_100%)] px-4 py-10 sm:px-6 lg:px-8">
    <div
        class="mx-auto w-full max-w-5xl rounded-3xl border border-slate-200/80 bg-white/95 shadow-[0_20px_60px_-25px_rgba(15,23,42,0.25)] backdrop-blur">
        <div class="grid grid-cols-1 lg:grid-cols-[1.1fr_0.9fr]">
            <section
                class="hidden rounded-l-3xl bg-linear-to-br from-slate-900 via-sky-900 to-emerald-800 p-10 text-white lg:flex lg:flex-col lg:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-cyan-100/85">SIBTA</p>
                    <h1 class="mt-3 text-3xl font-semibold leading-tight">Sistem Informasi Bimbingan Tugas Akhir</h1>
                    <p class="mt-4 text-sm text-slate-100/90">
                        Kelola proses bimbingan, jadwal sidang, dan progres mahasiswa dalam satu platform yang
                        terintegrasi.
                    </p>
                </div>

                <div class="space-y-3">
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                        <p class="text-xs uppercase tracking-wide text-cyan-100/80">Akses Cepat</p>
                        <p class="mt-1 text-sm text-slate-100/90">Masuk dengan Email, NIM, atau NIDN dan lanjutkan
                            pekerjaanmu.
                        </p>
                    </div>
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                        <p class="text-xs uppercase tracking-wide text-cyan-100/80">Keamanan</p>
                        <p class="mt-1 text-sm text-slate-100/90">Session aman dan autentikasi role-based untuk setiap
                            pengguna.</p>
                    </div>
                </div>
            </section>

            <section class="p-6 sm:p-10" x-data="{ showPassword: false }">
                <div class="mx-auto w-full max-w-md">
                    <img class="mx-auto h-16 w-auto rounded-2xl border border-slate-200 bg-white p-2 shadow-sm"
                        src="{{ asset('storage/logo/unpari.jpg') }}" alt="Logo SIBTA">
                    <h2 class="mt-5 text-center text-2xl font-semibold text-slate-900">Masuk ke SIBTA</h2>
                    <p class="mt-1 text-center text-sm text-slate-500">Gunakan akunmu untuk melanjutkan aktivitas.</p>

                    <form wire:submit.prevent="login" novalidate class="mt-7 space-y-5">
                        @csrf

                        <div>
                            <label for="email_nim" class="block text-sm font-medium text-slate-700">Email, NIM, atau
                                NIDN</label>
                            <input id="email_nim" type="text" wire:model.defer="email_nim"
                                autocomplete="username" placeholder="nama@email.com atau NIM/NIDN"
                                class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-200" />
                            @error('email_nim')
                                <x-ui.validation-error :message="$message" />
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700">Kata Sandi</label>
                            <div class="relative mt-1">
                                <input id="password" x-bind:type="showPassword ? 'text' : 'password'"
                                    wire:model.defer="password" autocomplete="current-password"
                                    placeholder="Masukkan kata sandi"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 pr-11 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-200" />

                                <button type="button" x-on:click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 inline-flex items-center px-3 text-slate-500 hover:text-slate-700"
                                    x-bind:aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'">
                                    <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <svg x-show="showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m3 3 18 18M10.477 10.484a3 3 0 0 0 4.036 4.043m2.062-2.06a3 3 0 0 0-4.12-4.12M6.228 6.235C4.074 7.71 2.6 9.87 2.036 11.683a1.012 1.012 0 0 0 0 .639C3.423 16.49 7.36 19.5 12 19.5a9.77 9.77 0 0 0 4.558-1.109M6.228 6.235A10.05 10.05 0 0 1 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639a11.035 11.035 0 0 1-2.03 3.357" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <x-ui.validation-error :message="$message" />
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" wire:model="remember"
                                    class="rounded border-slate-300 text-sky-600 focus:ring-sky-500" />
                                Ingat saya
                            </label>

                            <a href="{{ route('forgot-password.info') }}" wire:navigate
                                class="text-sm font-semibold text-sky-700 transition hover:text-sky-600">
                                Lupa kata sandi?
                            </a>
                        </div>

                        <button type="submit"
                            class="inline-flex w-full justify-center rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-300">
                            Masuk
                        </button>

                        <p class="text-center text-sm text-slate-500">
                            Jika butuh akses akun baru, hubungi administrator kampus.
                        </p>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>
