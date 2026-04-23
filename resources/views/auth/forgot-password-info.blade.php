<div
    class="min-h-screen bg-[radial-gradient(circle_at_top_left,rgba(14,165,233,0.14),transparent_32%),radial-gradient(circle_at_bottom_right,rgba(16,185,129,0.12),transparent_36%),linear-gradient(180deg,#f8fafc_0%,#eef6ff_100%)] px-4 py-10 sm:px-6 lg:px-8">
    <div
        class="mx-auto max-w-2xl rounded-3xl border border-slate-200 bg-white/95 p-8 shadow-[0_20px_60px_-25px_rgba(15,23,42,0.25)] backdrop-blur sm:p-10">
        <div class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-8">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m0 3.75h.007v.008H12v-.008ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>

        <div class="mt-6 text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-sky-700">Informasi Password</p>
            <h1 class="mt-3 text-3xl font-semibold text-slate-900">Lupa Kata Sandi</h1>
            <p class="mt-3 text-sm leading-6 text-slate-600 sm:text-base">
                Untuk mengubah atau mereset kata sandi akun SIBTA, silakan hubungi administrator atau operator kampus.
            </p>
        </div>

        <div class="mt-8 rounded-2xl border border-sky-200 bg-sky-50/80 p-5 text-sm text-slate-700">
            <p class="font-semibold text-slate-900">Yang perlu disiapkan saat menghubungi administrator:</p>
            <ul class="mt-3 list-disc space-y-2 pl-5">
                <li>Nama lengkap</li>
                <li>NIM atau email akun</li>
                <li>Peran akun: mahasiswa, dosen, atau admin</li>
                <li>Kendala singkat yang dialami saat login</li>
            </ul>
        </div>

        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
            <a href="{{ route('login') }}"
                class="inline-flex justify-center rounded-xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-sky-500">
                Kembali ke Login
            </a>
            <a href="{{ route('home') }}"
                class="inline-flex justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
