<div class="space-y-6">

    {{-- Hero --}}
    <section class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-blue-900 to-indigo-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="space-y-2">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-100/80">Portal Mahasiswa</p>
            <h1 class="text-3xl font-semibold sm:text-4xl">Form Pengajuan</h1>
            <p class="max-w-2xl text-sm text-indigo-100 sm:text-base">
                Ajukan judul skripsi dan kelola pengajuan dosen pembimbing Anda dari satu halaman.
            </p>
        </div>
    </section>

    {{-- Info pembimbing aktif --}}
    @if ($pembimbingAktif)
        <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-emerald-100">
                <svg class="size-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-emerald-800">
                    Dosen Pembimbing: {{ $pembimbingAktif->dosen?->user?->name ?? '-' }}
                </p>
                <p class="text-xs text-emerald-600">Sudah ditetapkan — dosen ini yang mereview judul skripsi Anda</p>
            </div>
        </div>
    @else
        <div class="flex items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-amber-100">
                <svg class="size-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>
            <p class="text-sm text-amber-800">
                <span class="font-semibold">Dosen pembimbing belum ditetapkan.</span>
                Gunakan tab <span class="font-semibold">Pengajuan Dosen</span> untuk mengajukan dosen yang Anda inginkan ke kaprodi.
            </p>
        </div>
    @endif

    {{-- Tab Navigation --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex border-b border-slate-200">
            @foreach ([
                'judul'      => ['label' => 'Pengajuan Judul',  'icon' => 'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10'],
                'pembimbing' => ['label' => 'Pengajuan Dosen',  'icon' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'],
                'riwayat'    => ['label' => 'Riwayat Judul',    'icon' => 'M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z'],
            ] as $tab => $meta)
                <button wire:click="$set('activeTab', '{{ $tab }}')"
                    class="flex items-center gap-2 flex-1 justify-center px-4 py-3.5 text-sm font-semibold transition-colors
                        {{ $activeTab === $tab
                            ? 'border-b-2 border-blue-600 text-blue-700 bg-blue-50/50'
                            : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                    <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $meta['icon'] }}" />
                    </svg>
                    <span class="hidden sm:inline">{{ $meta['label'] }}</span>
                </button>
            @endforeach
        </div>

        {{-- ============================================================
             TAB 1: PENGAJUAN JUDUL
        ============================================================ --}}
        @if ($activeTab === 'judul')
            <div class="p-6">
                <h2 class="text-base font-semibold text-slate-900">Pengajuan Judul Skripsi</h2>
                <p class="mt-1 text-sm text-slate-500">
                    NIM: {{ $mahasiswa->nim }} &bull; {{ $mahasiswa->prodi }}
                </p>

                @if ($approvedTitle)
                    <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                        <p class="text-sm font-semibold text-emerald-800">Judul sudah disetujui</p>
                        <p class="mt-1 font-medium text-slate-800">{{ $approvedTitle->judul }}</p>
                        <p class="mt-1 text-xs text-emerald-700">Form pengajuan judul baru tidak tersedia.</p>
                    </div>
                @else
                    <form wire:submit="saveJudul" novalidate class="mt-5 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Judul Skripsi <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="judul"
                                placeholder="Contoh: Sistem Informasi Monitoring Skripsi Berbasis Web"
                                class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                            @error('judul') <x-ui.validation-error :message="$message" /> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Deskripsi Singkat <span class="text-slate-400">(opsional)</span></label>
                            <textarea wire:model="deskripsi" rows="4"
                                placeholder="Tuliskan latar belakang singkat, metode, atau ruang lingkup penelitian"
                                class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"></textarea>
                            @error('deskripsi') <x-ui.validation-error :message="$message" /> @enderror
                        </div>

                        @if (! $pembimbingAktif)
                            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                                Judul yang diajukan akan diulas oleh dosen setelah pembimbing ditetapkan oleh kaprodi.
                            </div>
                        @endif

                        <div class="flex justify-end">
                            <button type="submit"
                                class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                                Kirim Pengajuan Judul
                            </button>
                        </div>
                    </form>
                @endif
            </div>

        {{-- ============================================================
             TAB 2: PENGAJUAN DOSEN PEMBIMBING
        ============================================================ --}}
        @elseif ($activeTab === 'pembimbing')
            <div class="p-6">
                <h2 class="text-base font-semibold text-slate-900">Pengajuan Dosen Pembimbing</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Ajukan dosen yang Anda inginkan sebagai pembimbing. Kaprodi akan meninjau dan memutuskan.
                </p>

                @if ($pembimbingAktif)
                    <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                        <span class="font-semibold">Pembimbing aktif saat ini:</span>
                        {{ $pembimbingAktif->dosen?->user?->name ?? '-' }}
                        <span class="text-slate-500">(NIDN: {{ $pembimbingAktif->dosen?->nidn ?? '-' }})</span>
                    </div>
                @endif

                @if ($pendingPembimbing)
                    <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        <span class="font-semibold">Ada pengajuan pembimbing yang sedang diproses kaprodi.</span>
                        Tunggu hasilnya sebelum mengajukan kembali.
                    </div>
                @else
                    <form wire:submit="savePengajuanPembimbing" novalidate class="mt-5 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Pilih Dosen <span class="text-red-500">*</span></label>
                            <p class="mt-0.5 text-xs text-slate-400">Dosen yang sudah menjadi pembimbing aktif Anda tidak ditampilkan.</p>
                            <select wire:model="dosenIdPengajuan"
                                class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                                <option value="">-- Pilih Dosen --</option>
                                @foreach ($dosenOptions as $d)
                                    <option value="{{ $d['id'] }}"
                                        @disabled($d['sisa'] <= 0)>
                                        {{ $d['name'] }} ({{ $d['nidn'] }})
                                        — Sisa kuota: {{ $d['sisa'] }}
                                        @if ($d['sisa'] <= 0) [PENUH] @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('dosenIdPengajuan') <x-ui.validation-error :message="$message" /> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Alasan Pengajuan <span class="text-red-500">*</span></label>
                            <textarea wire:model="alasanPengajuan" rows="4"
                                placeholder="Jelaskan alasan Anda ingin dibimbing oleh dosen tersebut, atau alasan pergantian pembimbing"
                                class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"></textarea>
                            @error('alasanPengajuan') <x-ui.validation-error :message="$message" /> @enderror
                        </div>

                        <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                            Pengajuan ini akan dikirim ke kaprodi prodi <strong>{{ $mahasiswa->prodi }}</strong>.
                            Jika disetujui, dosen pembimbing Anda akan otomatis diganti.
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                                Kirim Pengajuan Pembimbing
                            </button>
                        </div>
                    </form>
                @endif

                {{-- Riwayat pengajuan pembimbing --}}
                @if ($riwayatPembimbing->isNotEmpty())
                    <div class="mt-8">
                        <h3 class="text-sm font-semibold text-slate-700">Riwayat Pengajuan Pembimbing</h3>
                        <div class="mt-3 space-y-2">
                            @foreach ($riwayatPembimbing as $rp)
                                @php
                                    $rpColor = match ($rp->status) {
                                        'approved' => 'bg-emerald-100 text-emerald-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        default    => 'bg-amber-100 text-amber-700',
                                    };
                                    $rpLabel = match ($rp->status) {
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                        default    => 'Menunggu',
                                    };
                                @endphp
                                <div class="rounded-xl border border-slate-200 px-4 py-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-slate-900">
                                                {{ $rp->dosen?->user?->name ?? '-' }}
                                            </p>
                                            <p class="text-xs text-slate-500">NIDN: {{ $rp->dosen?->nidn ?? '-' }}</p>
                                            <p class="mt-1 text-xs text-slate-400">
                                                Diajukan {{ $rp->diajukan_pada?->translatedFormat('d M Y H:i') ?? '-' }}
                                            </p>
                                        </div>
                                        <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $rpColor }}">
                                            {{ $rpLabel }}
                                        </span>
                                    </div>
                                    @if ($rp->alasan)
                                        <p class="mt-2 text-xs text-slate-500">
                                            <span class="font-medium text-slate-700">Alasan:</span> {{ $rp->alasan }}
                                        </p>
                                    @endif
                                    @if ($rp->catatan_kaprodi)
                                        <div class="mt-2 rounded-lg {{ $rp->status === 'rejected' ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700' }} px-3 py-2 text-xs">
                                            <span class="font-semibold">Catatan Kaprodi:</span> {{ $rp->catatan_kaprodi }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        {{-- ============================================================
             TAB 3: RIWAYAT PENGAJUAN JUDUL
        ============================================================ --}}
        @elseif ($activeTab === 'riwayat')
            <div class="p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Riwayat Pengajuan Judul</h2>
                        <p class="text-sm text-slate-500">Status setiap judul yang pernah diajukan.</p>
                    </div>
                    <div class="flex gap-2">
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Cari judul..."
                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 sm:w-48" />
                        <select wire:model.live="statusFilter"
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <option value="">Semua</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="revisi">Revisi</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($pengajuanList as $pengajuan)
                        @php
                            $st = strtolower($pengajuan->status ?? 'pending');
                            $stClass = match ($st) {
                                'approved', 'disetujui' => 'bg-emerald-100 text-emerald-700',
                                'rejected', 'ditolak'  => 'bg-red-100 text-red-700',
                                'revisi'               => 'bg-orange-100 text-orange-700',
                                default                => 'bg-amber-100 text-amber-700',
                            };
                        @endphp
                        <div class="rounded-2xl border border-slate-200 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold text-slate-900">{{ $pengajuan->judul }}</p>
                                    <p class="mt-0.5 text-xs text-slate-400">
                                        {{ $pengajuan->created_at?->translatedFormat('d M Y H:i') }}
                                        @if (($pengajuan->revisi_ke ?? 0) > 0)
                                            &bull; Revisi ke-{{ $pengajuan->revisi_ke }}
                                        @endif
                                    </p>
                                </div>
                                <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $stClass }}">
                                    {{ ucfirst($pengajuan->status ?? 'pending') }}
                                </span>
                            </div>

                            @if ($pengajuan->deskripsi)
                                <p class="mt-2 text-sm text-slate-600">{{ $pengajuan->deskripsi }}</p>
                            @endif

                            @if ($pengajuan->catatan)
                                <div class="mt-2 rounded-xl bg-slate-50 px-3 py-2 text-sm text-slate-600">
                                    <span class="font-medium text-slate-700">Catatan Dosen:</span>
                                    {{ $pengajuan->catatan }}
                                </div>
                            @endif

                            @if ($pengajuan->catatan_revisi_mahasiswa)
                                <div class="mt-2 rounded-xl bg-blue-50 px-3 py-2 text-sm text-blue-700">
                                    <span class="font-medium text-blue-800">Catatan Revisi Saya:</span>
                                    {{ $pengajuan->catatan_revisi_mahasiswa }}
                                </div>
                            @endif

                            {{-- Tombol revisi --}}
                            @php $canRevise = in_array($st, ['revisi', 'rejected', 'ditolak'], true); @endphp
                            @if ($canRevise)
                                @if (! in_array($pengajuan->id, $editingRevisionIds, true))
                                    <div class="mt-3">
                                        <button wire:click="mulaiRevisi({{ $pengajuan->id }})"
                                            class="rounded-xl bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">
                                            Perbaiki Judul
                                        </button>
                                    </div>
                                @else
                                    <div class="mt-4 space-y-3 rounded-2xl border border-blue-200 bg-blue-50/50 p-4">
                                        <p class="text-sm font-semibold text-slate-800">Form Revisi Judul</p>

                                        <div>
                                            <label class="mb-1 block text-xs font-medium text-slate-700">Judul Revisi</label>
                                            <input type="text" wire:model="revisiJudul.{{ $pengajuan->id }}"
                                                placeholder="Masukkan judul revisi"
                                                class="block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                                            @error('revisiJudul.' . $pengajuan->id) <x-ui.validation-error :message="$message" /> @enderror
                                        </div>

                                        <div>
                                            <label class="mb-1 block text-xs font-medium text-slate-700">Deskripsi</label>
                                            <textarea wire:model="revisiDeskripsi.{{ $pengajuan->id }}" rows="3"
                                                placeholder="Perbarui deskripsi singkat penelitian"
                                                class="block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"></textarea>
                                            @error('revisiDeskripsi.' . $pengajuan->id) <x-ui.validation-error :message="$message" /> @enderror
                                        </div>

                                        <div>
                                            <label class="mb-1 block text-xs font-medium text-slate-700">Catatan ke Dosen <span class="text-slate-400">(opsional)</span></label>
                                            <textarea wire:model="revisiCatatan.{{ $pengajuan->id }}" rows="2"
                                                placeholder="Jelaskan apa yang sudah diperbaiki"
                                                class="block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"></textarea>
                                        </div>

                                        <div class="flex gap-2">
                                            <button wire:click="kirimRevisi({{ $pengajuan->id }})"
                                                class="rounded-xl bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                                                Kirim Revisi
                                            </button>
                                            <button wire:click="batalRevisi({{ $pengajuan->id }})"
                                                class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                                Batal
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-sm text-slate-400">
                            Belum ada pengajuan judul.
                        </div>
                    @endforelse
                </div>

                @if ($pengajuanList->hasPages())
                    <div class="mt-4">
                        {{ $pengajuanList->links('vendor.pagination.tailwind') }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
