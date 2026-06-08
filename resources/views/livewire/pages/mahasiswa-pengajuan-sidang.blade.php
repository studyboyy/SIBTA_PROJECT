<div class="space-y-8">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-emerald-900 to-teal-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-emerald-100/80">Portal Mahasiswa</p>
            <h1 class="text-3xl font-semibold sm:text-4xl">Pengajuan Sidang</h1>
            <p class="max-w-2xl text-sm text-emerald-100 sm:text-base">Ajukan sidang setelah seluruh syarat kelengkapan
                terpenuhi dan disetujui dosen pembimbing.</p>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 2xl:grid-cols-[1fr_1fr]">
        {{-- Checklist Kelayakan --}}
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Syarat Kelayakan Sidang</h2>
            <p class="mt-1 text-sm text-slate-500">Dokumen-dokumen berikut harus sudah disetujui dosen pembimbing.</p>

            <div class="mt-5 space-y-3">
                @php
                    $syaratLabels = [
                        'proposal' => 'Dokumen proposal skripsi disetujui dosen',
                        'skripsi'  => 'Dokumen skripsi (laporan akhir) disetujui dosen',
                    ];
                @endphp
                @foreach ($syaratLabels as $key => $label)
                    <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
                        <p class="text-sm text-slate-700">{{ $label }}</p>
                        @if ($checklist[$key])
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Terpenuhi</span>
                        @else
                            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">Belum</span>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Info status jadwal sidang (bukan syarat, hanya informasi) --}}
            <div class="mt-5">
                <h3 class="text-sm font-semibold text-slate-700">Informasi Jadwal Sidang</h3>
                @if (! $checklist['belum_dijadwalkan'])
                    <div class="mt-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                        Anda sudah memiliki jadwal sidang yang ditetapkan. Tidak perlu mengajukan lagi.
                    </div>
                @else
                    <div class="mt-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                        Jadwal sidang belum ditetapkan. Setelah pengajuan disetujui, admin akan menjadwalkan sidang Anda.
                    </div>
                @endif
            </div>
        </article>

        {{-- Form Pengajuan --}}
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Status & Form Pengajuan</h2>

            @if ($pengajuan)
                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Status Dosen</p>
                        @php
                            $sdColor = match(strtolower($pengajuan->status_dosen ?? 'pending')) {
                                'approved' => 'text-emerald-700',
                                'rejected' => 'text-red-700',
                                'revisi'   => 'text-orange-700',
                                default    => 'text-amber-700',
                            };
                        @endphp
                        <p class="mt-1 text-sm font-semibold {{ $sdColor }}">
                            {{ ucfirst($pengajuan->status_dosen ?? 'Menunggu') }}
                        </p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Status Kaprodi</p>
                        @php
                            $skColor = match(strtolower($pengajuan->status_kaprodi ?? 'pending')) {
                                'approved' => 'text-emerald-700',
                                'rejected' => 'text-red-700',
                                default    => 'text-amber-700',
                            };
                        @endphp
                        <p class="mt-1 text-sm font-semibold {{ $skColor }}">
                            {{ ucfirst($pengajuan->status_kaprodi ?? 'Menunggu') }}
                        </p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 px-4 py-3 sm:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Status Admin / Penjadwalan</p>
                        @php
                            $saColor = match(strtolower($pengajuan->status ?? 'pending')) {
                                'approved' => 'text-emerald-700',
                                'rejected' => 'text-red-700',
                                default    => 'text-amber-700',
                            };
                        @endphp
                        <p class="mt-1 text-sm font-semibold {{ $saColor }}">
                            {{ ucfirst($pengajuan->status ?? 'Menunggu') }}
                            @if ($pengajuan->gelombang)
                                — Gelombang {{ $pengajuan->gelombang }}
                            @endif
                        </p>
                    </div>
                </div>

                @if ($pengajuan->catatan_dosen)
                    <div class="mt-4 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                        <span class="font-semibold">Catatan Dosen:</span> {{ $pengajuan->catatan_dosen }}
                    </div>
                @endif

                @if ($pengajuan->catatan_kaprodi)
                    <div class="mt-3 rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-800">
                        <span class="font-semibold">Catatan Kaprodi:</span> {{ $pengajuan->catatan_kaprodi }}
                    </div>
                @endif

                @if ($pengajuan->catatan_admin)
                    <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        <span class="font-semibold">Catatan Admin:</span> {{ $pengajuan->catatan_admin }}
                    </div>
                @endif
            @else
                <p class="mt-4 text-sm text-slate-500">Anda belum pernah mengajukan sidang.</p>
            @endif

            {{-- Tombol/Form hanya tampil jika: dokumen lengkap AND belum punya jadwal AND (belum mengajukan atau pengajuan ditolak) --}}
            @php
                $sudahPunyaJadwal    = ! $checklist['belum_dijadwalkan'];
                $dokumenLengkap      = $checklist['proposal'] && $checklist['skripsi'];
                $pengajuanApproved   = ($pengajuan?->status === 'approved');
                $pengajuanSedangDiproses = $pengajuan && ! $pengajuanApproved && ! $canResubmit;
                $bolehAjukan         = $dokumenLengkap && ! $sudahPunyaJadwal && ! $pengajuanApproved && ! $pengajuanSedangDiproses;
            @endphp

            @if ($sudahPunyaJadwal)
                <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    Jadwal sidang Anda sudah ditetapkan. Silakan pantau di halaman dashboard.
                </div>
            @elseif ($pengajuanApproved)
                <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    Pengajuan sidang Anda sudah disetujui. Admin sedang memproses penjadwalan.
                </div>
            @elseif ($pengajuanSedangDiproses)
                <div class="mt-5 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                    Pengajuan sidang sedang diproses. Tunggu keputusan dosen, kaprodi, atau admin sebelum mengirim ulang.
                </div>
            @elseif ($bolehAjukan)
                <form wire:submit.prevent="submit" novalidate class="mt-5 space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Catatan Pengajuan (opsional)</label>
                        <textarea wire:model="catatan_mahasiswa" rows="4"
                            placeholder="Contoh: semua dokumen telah diperbaiki sesuai arahan dosen"
                            class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100"></textarea>
                        @error('catatan_mahasiswa')
                            <x-ui.validation-error :message="$message" />
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-500">
                            {{ $pengajuan ? 'Ajukan Ulang' : 'Ajukan Sidang' }}
                        </button>
                    </div>
                </form>
            @else
                <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    Lengkapi dan pastikan dokumen proposal serta skripsi sudah disetujui dosen pembimbing sebelum mengajukan sidang.
                </div>
            @endif

            <p class="mt-3 text-xs text-slate-500">Terakhir diajukan:
                {{ $pengajuan?->diajukan_pada?->translatedFormat('d M Y H:i') ?? '-' }}</p>
        </article>
    </section>
</div>
