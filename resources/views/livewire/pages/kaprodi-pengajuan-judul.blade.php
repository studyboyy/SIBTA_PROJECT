<div class="space-y-8">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-cyan-900 to-teal-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-cyan-100/80">Panel Kaprodi</p>
            <h1 class="text-3xl font-semibold sm:text-4xl">Pengajuan Judul & Pembimbing</h1>
            <p class="max-w-2xl text-sm text-cyan-100 sm:text-base">
                Lihat pengajuan judul skripsi mahasiswa di prodi Anda beserta calon dosen pembimbing yang diinginkan.
                Tetapkan dosen pembimbing sesuai rekomendasi mahasiswa atau ganti sesuai kebutuhan.
            </p>
        </div>
    </section>

    @if ($managedProdi)
        <div class="rounded-2xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-800">
            Menampilkan pengajuan mahasiswa prodi:
            <span class="font-semibold">{{ $managedProdi->name }}</span>
        </div>
    @endif

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Daftar Pengajuan Judul</h2>
                <p class="text-sm text-slate-500">Cek pengajuan mahasiswa dan tetapkan dosen pembimbing.</p>
            </div>
            <div class="grid w-full gap-3 sm:grid-cols-2 lg:max-w-xl">
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama, NIM, atau judul"
                    class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-100" />
                <select wire:model.live="statusFilter"
                    class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-100">
                    <option value="">Semua status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="revisi">Revisi</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>

        <div class="mt-6 space-y-4">
            @forelse ($pengajuanList as $pengajuan)
                @php
                    $statusPengajuan = strtolower($pengajuan->status ?? 'pending');
                    $statusClass = match ($statusPengajuan) {
                        'approved', 'disetujui' => 'bg-emerald-100 text-emerald-700',
                        'rejected', 'ditolak' => 'bg-red-100 text-red-700',
                        'revisi' => 'bg-orange-100 text-orange-700',
                        default => 'bg-amber-100 text-amber-700',
                    };
                    $pembimbingAktif = $pengajuan->mahasiswa?->bimbingans?->first()?->dosen?->user?->name;
                @endphp
                <article class="rounded-2xl border border-slate-200 p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $pengajuan->judul }}</p>
                            <p class="mt-1 text-sm text-slate-500">
                                {{ $pengajuan->mahasiswa?->user?->name ?? '-' }}
                                &bull; {{ $pengajuan->mahasiswa?->nim ?? '-' }}
                            </p>
                            <p class="mt-1 text-xs text-slate-400">
                                Diajukan {{ $pengajuan->created_at?->translatedFormat('d M Y H:i') }}
                            </p>
                        </div>
                        <span class="self-start rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                            {{ ucfirst($pengajuan->status ?? 'pending') }}
                        </span>
                    </div>

                    {{-- Info Pembimbing --}}
                    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="rounded-xl border border-slate-200 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Calon Dosen Pilihan Mahasiswa</p>
                            @if ($pengajuan->calonDosenPembimbing)
                                <p class="mt-1 text-sm font-semibold text-indigo-700">
                                    {{ $pengajuan->calonDosenPembimbing->user->name ?? '-' }}
                                </p>
                                <p class="text-xs text-slate-400">
                                    NIDN: {{ $pengajuan->calonDosenPembimbing->nidn }}</p>
                            @else
                                <p class="mt-1 text-sm text-slate-400">Tidak dipilih</p>
                            @endif
                        </div>

                        <div class="rounded-xl border border-slate-200 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Pembimbing Ditetapkan</p>
                            @if ($pembimbingAktif)
                                <p class="mt-1 text-sm font-semibold text-emerald-700">
                                    {{ $pembimbingAktif }}
                                </p>
                                <p class="text-xs text-slate-400">Sudah ditetapkan</p>
                            @else
                                <p class="mt-1 text-sm text-amber-700 font-semibold">Belum ditetapkan</p>
                            @endif
                        </div>
                    </div>

                    @if ($pengajuan->deskripsi)
                        <p class="mt-3 text-sm text-slate-600">{{ $pengajuan->deskripsi }}</p>
                    @endif

                    {{-- Tombol tetapkan dosen --}}
                    <div class="mt-4">
                        <button wire:click="bukaModalTetapkan({{ $pengajuan->id }})"
                            class="rounded-xl bg-cyan-600 px-4 py-2 text-xs font-semibold text-white hover:bg-cyan-700">
                            {{ $pembimbingAktif ? 'Ganti Dosen Pembimbing' : 'Tetapkan Dosen Pembimbing' }}
                        </button>
                    </div>
                </article>
            @empty
                <div
                    class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                    Belum ada pengajuan judul di prodi ini.
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            <x-ui.show-entries wire:model.live="perPage" class="focus:border-cyan-500 focus:ring-cyan-500" />
        </div>
        <div class="mt-3">
            {{ $pengajuanList->links('vendor.pagination.tailwind') }}
        </div>
    </section>

    {{-- Modal Tetapkan Dosen --}}
    <livewire:components.modal name="kaprodi-tetapkan-dosen">
        <div class="w-full">
            <h3 class="text-center text-lg font-semibold text-slate-900">Tetapkan Dosen Pembimbing</h3>
            <p class="mt-2 text-center text-sm text-slate-500">
                Pilih dosen yang akan menjadi pembimbing mahasiswa ini. Jika mahasiswa sudah mengusulkan calon dosen,
                pilihan tersebut akan ditampilkan terlebih dahulu.
            </p>

            <div class="mt-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Dosen Pembimbing</label>
                <select wire:model="dosenTerpilihId"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-100">
                    <option value="">-- Pilih Dosen Pembimbing --</option>
                    @foreach ($dosenOptions as $dsn)
                        <option value="{{ $dsn['id'] }}">
                            {{ $dsn['name'] }} ({{ $dsn['nidn'] }}) — Sisa kuota: {{ $dsn['sisa'] }}
                        </option>
                    @endforeach
                </select>
                @error('dosenTerpilihId')
                    <x-ui.validation-error :message="$message" />
                @enderror
            </div>

            <div class="mt-5 flex justify-end gap-3">
                <button wire:click="$dispatch('close-modal', { name: 'kaprodi-tetapkan-dosen' })"
                    class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Batal
                </button>
                <button wire:click="tetapkanDosen"
                    class="rounded-xl bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700">
                    Tetapkan
                </button>
            </div>
        </div>
    </livewire:components.modal>
</div>
