<div class="space-y-8">
    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-indigo-900 to-blue-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-indigo-100/80">Portal Mahasiswa</p>
            <h1 class="text-3xl font-semibold sm:text-4xl">Bimbingan Saya</h1>
            <p class="max-w-2xl text-sm text-indigo-100 sm:text-base">
                Pantau penjadwalan bimbingan bersama dosen pembimbing dan konfirmasi kehadiran setiap sesi.
            </p>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Progress Bimbingan</h2>
                <p class="text-sm text-slate-500">Kehadiran Anda pada jadwal bimbingan dari dosen.</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-slate-500">{{ $totalHadir }}/{{ $totalBimbingan }} pertemuan hadir</p>
                <p class="text-2xl font-semibold text-slate-900">{{ $progressBimbingan }}%</p>
            </div>
        </div>
        <div class="mt-3 h-3 rounded-full bg-slate-100">
            <div class="h-3 rounded-full bg-linear-to-r from-blue-600 to-cyan-500"
                style="width: {{ $progressBimbingan > 0 ? max($progressBimbingan, 6) : 0 }}%"></div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Riwayat Bimbingan</h2>
                <p class="text-sm text-slate-500">NIM: {{ $mahasiswa->nim }} • {{ $mahasiswa->prodi }}</p>
            </div>
            <div class="w-full lg:max-w-xl">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari catatan atau nama dosen"
                    class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
            </div>
        </div>

        <div class="mt-6 space-y-3">
            @forelse ($bimbinganList as $bimbingan)
                <div class="rounded-2xl border border-slate-200 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-900">
                                {{ $bimbingan->dosen?->user?->name ?? 'Dosen Pembimbing' }}</p>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ $bimbingan->tanggal ? \Carbon\Carbon::parse($bimbingan->tanggal)->translatedFormat('d M Y') : '-' }}
                                {{ $bimbingan->jam ? ' • ' . \Carbon\Carbon::parse($bimbingan->jam)->format('H:i') : '' }}
                            </p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold bg-blue-100 text-blue-700">
                            Penjadwalan Bimbingan
                        </span>
                    </div>
                    <p class="mt-3 text-sm text-slate-600">{{ $bimbingan->catatan ?: 'Belum ada agenda bimbingan.' }}
                    </p>

                    @php
                        $catatanDosen = $bimbingan->bimbinganMessages
                            ->where('sender_role', 'dosen')
                            ->where('dosen_id', $bimbingan->dosen_id)
                            ->sortByDesc('created_at')
                            ->first();
                    @endphp

                    @if ($catatanDosen)
                        <div class="mt-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-xs font-semibold uppercase tracking-wide text-amber-800">Catatan Revisi
                                    dari Dosen</p>
                                <p class="text-[11px] text-amber-700">
                                    {{ $catatanDosen->created_at?->translatedFormat('d M Y H:i') }}
                                </p>
                            </div>
                            <p class="mt-2 whitespace-pre-line text-sm text-amber-900">{{ $catatanDosen->message }}</p>
                            @if (($bimbingan->bimbinganMessages->where('sender_role', 'dosen')->count() ?? 0) > 1)
                                <p class="mt-2 text-xs text-amber-700">
                                    Ada {{ $bimbingan->bimbinganMessages->where('sender_role', 'dosen')->count() }}
                                    catatan dari dosen untuk sesi ini.
                                </p>
                            @endif
                        </div>
                    @endif

                    <div class="mt-3 rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-sky-800">Catatan Hasil
                                Bimbingan (Mahasiswa)</p>
                            <p class="text-[11px] text-sky-700">Sesi
                                {{ $bimbingan->tanggal ? \Carbon\Carbon::parse($bimbingan->tanggal)->translatedFormat('d M Y') : '-' }}{{ $bimbingan->jam ? ' • ' . \Carbon\Carbon::parse($bimbingan->jam)->format('H:i') : '' }}
                            </p>
                        </div>

                        <textarea wire:model.defer="catatanMahasiswaDraft.{{ $bimbingan->id }}" rows="3"
                            placeholder="Tuliskan ringkasan hasil bimbingan untuk sesi ini..."
                            class="mt-2 block w-full rounded-xl border border-sky-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100"></textarea>
                        @error('catatanMahasiswaDraft.' . $bimbingan->id)
                            <x-ui.validation-error :message="$message" />
                        @enderror

                        <div class="mt-2 flex items-center justify-between gap-2">
                            <button wire:click="simpanCatatanHasil({{ $bimbingan->id }})" wire:loading.attr="disabled"
                                class="rounded-lg bg-sky-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-sky-700 disabled:opacity-60">
                                <span wire:loading.remove wire:target="simpanCatatanHasil({{ $bimbingan->id }})">Simpan
                                    Catatan</span>
                                <span wire:loading
                                    wire:target="simpanCatatanHasil({{ $bimbingan->id }})">Menyimpan...</span>
                            </button>
                            @if (!empty($bimbingan->catatan_mahasiswa))
                                <span class="text-[11px] text-sky-800">Catatan sudah tersimpan.</span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-2 flex flex-wrap gap-2 text-xs">
                        <span
                            class="rounded-full px-2.5 py-1 {{ ($bimbingan->status_sesi ?? 'diajukan') === 'selesai' ? 'bg-emerald-100 text-emerald-700' : (($bimbingan->status_sesi ?? 'diajukan') === 'disetujui' ? 'bg-blue-100 text-blue-700' : (($bimbingan->status_sesi ?? 'diajukan') === 'dibatalkan' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700')) }}">
                            Status Sesi: {{ ucfirst($bimbingan->status_sesi ?? 'diajukan') }}
                        </span>
                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-slate-700">
                            Mode: {{ ucfirst($bimbingan->mode ?? 'offline') }}
                        </span>
                        @if (($bimbingan->mode ?? 'offline') === 'online' && $bimbingan->link_online)
                            <a href="{{ $bimbingan->link_online }}" target="_blank"
                                class="rounded-full bg-blue-50 px-2.5 py-1 font-semibold text-blue-700">Link Meeting</a>
                        @elseif (($bimbingan->mode ?? 'offline') === 'offline')
                            <span class="rounded-full bg-blue-50 px-2.5 py-1 text-blue-700">
                                Lokasi: {{ $bimbingan->lokasi ?: 'Belum ditentukan' }}
                            </span>
                        @endif
                        <span
                            class="rounded-full px-2.5 py-1 {{ ($bimbingan->konfirmasi_mahasiswa ?? 'pending') === 'hadir' ? 'bg-emerald-100 text-emerald-700' : (($bimbingan->konfirmasi_mahasiswa ?? 'pending') === 'tidak_hadir' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                            Kehadiran:
                            {{ ($bimbingan->konfirmasi_mahasiswa ?? 'pending') === 'hadir' ? 'Hadir' : (($bimbingan->konfirmasi_mahasiswa ?? 'pending') === 'tidak_hadir' ? 'Tidak Hadir' : 'Menunggu Konfirmasi') }}
                        </span>
                    </div>

                    @if (($bimbingan->konfirmasi_mahasiswa ?? 'pending') === 'pending')
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button wire:click="konfirmasiKehadiran({{ $bimbingan->id }}, 'hadir')"
                                class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">
                                Saya Hadir
                            </button>
                            <button wire:click="konfirmasiKehadiran({{ $bimbingan->id }}, 'tidak_hadir')"
                                class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700">
                                Tidak Hadir
                            </button>
                        </div>
                    @endif
                </div>
            @empty
                <div
                    class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                    Belum ada data bimbingan.
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            <x-ui.show-entries wire:model.live="perPage" class="focus:border-blue-500 focus:ring-blue-500" />
        </div>

        <div class="mt-3">
            {{ $bimbinganList->links('vendor.pagination.tailwind') }}
        </div>
    </section>
</div>
