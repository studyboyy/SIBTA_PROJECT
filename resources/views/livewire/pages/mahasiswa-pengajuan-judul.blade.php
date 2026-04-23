<div class="space-y-8">
    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-blue-900 to-indigo-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-indigo-100/80">Portal Mahasiswa</p>
            <h1 class="text-3xl font-semibold sm:text-4xl">Pengajuan Judul</h1>
            <p class="max-w-2xl text-sm text-indigo-100 sm:text-base">
                Ajukan judul tugas akhir, pantau status review, dan lihat catatan dari dosen pembimbing Anda.
            </p>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900">Form Pengajuan Baru</h2>
        <p class="mt-1 text-sm text-slate-500">NIM: {{ $mahasiswa->nim }} • {{ $mahasiswa->prodi }}</p>
        <p class="mt-1 text-sm text-slate-500">
            Dosen pembimbing:
            @if ($primaryPembimbingName)
                <span class="font-semibold text-slate-700">{{ $primaryPembimbingName }}</span>
            @else
                <span class="font-semibold text-amber-700">Belum ditentukan</span>
            @endif
        </p>

        @if ($approvedTitle)
            <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm text-emerald-800">
                <p class="font-semibold">Judul sudah disetujui dosen</p>
                <p class="mt-1">{{ $approvedTitle->judul }}</p>
                <p class="mt-2 text-xs text-emerald-700">
                    Form pengajuan baru disembunyikan karena Anda sudah memiliki judul tugas akhir yang disetujui.
                </p>
            </div>
        @else
            <div class="mt-4 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                Konfirmasi pengajuan judul hanya dapat dilakukan oleh dosen pembimbing yang terkait dengan mahasiswa
                ini.
            </div>

            <form wire:submit="save" class="mt-5 space-y-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Judul</label>
                    <input type="text" wire:model="judul"
                        placeholder="Contoh: Sistem Monitoring Bimbingan Berbasis Web"
                        class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                    @error('judul')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Deskripsi Singkat (Opsional)</label>
                    <textarea wire:model="deskripsi" rows="4"
                        placeholder="Tuliskan latar belakang singkat, metode, atau ruang lingkup penelitian"
                        class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"></textarea>
                    @error('deskripsi')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
        @endif
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Riwayat Pengajuan</h2>
                <p class="text-sm text-slate-500">Status terbaru untuk setiap judul yang pernah diajukan.</p>
            </div>
            <div class="grid w-full gap-3 sm:grid-cols-2 lg:max-w-xl">
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Cari judul, deskripsi, atau catatan"
                    class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                <select wire:model.live="status"
                    class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="">Semua status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="revisi">Revisi</option>
                </select>
            </div>
        </div>

        <div class="mt-6 space-y-3">
            @forelse ($pengajuanList as $pengajuan)
                @php
                    $status = strtolower($pengajuan->status ?? 'pending');
                    $statusClass = match ($status) {
                        'approved', 'disetujui' => 'bg-emerald-100 text-emerald-700',
                        'rejected', 'ditolak' => 'bg-red-100 text-red-700',
                        'revisi' => 'bg-orange-100 text-orange-700',
                        default => 'bg-amber-100 text-amber-700',
                    };
                @endphp
                <div class="rounded-2xl border border-slate-200 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $pengajuan->judul }}</p>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ $pengajuan->created_at?->translatedFormat('d M Y H:i') }}</p>
                            @if (($pengajuan->revisi_ke ?? 0) > 0)
                                <p class="mt-1 text-xs text-blue-600">
                                    Revisi ke-{{ $pengajuan->revisi_ke }} dikirim
                                    {{ $pengajuan->revisi_dikirim_pada?->translatedFormat('d M Y H:i') ?? '-' }}
                                </p>
                            @endif
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                            {{ ucfirst($pengajuan->status ?? 'pending') }}
                        </span>
                    </div>

                    @if ($pengajuan->deskripsi)
                        <p class="mt-3 text-sm text-slate-600">{{ $pengajuan->deskripsi }}</p>
                    @endif

                    <div class="mt-3 rounded-xl bg-slate-50 px-3 py-2 text-sm text-slate-600">
                        <span class="font-medium text-slate-700">Catatan Reviewer:</span>
                        {{ $pengajuan->catatan ?: 'Belum ada catatan.' }}
                    </div>

                    @if ($pengajuan->catatan_revisi_mahasiswa)
                        <div class="mt-3 rounded-xl bg-blue-50 px-3 py-2 text-sm text-blue-700">
                            <span class="font-medium text-blue-800">Catatan Revisi Saya:</span>
                            {{ $pengajuan->catatan_revisi_mahasiswa }}
                        </div>
                    @endif

                    @php
                        $canRevise = in_array($status, ['revisi', 'rejected', 'ditolak'], true);
                        $isEditingRevision = in_array($pengajuan->id, $editingRevisionIds ?? [], true);
                    @endphp

                    @if ($canRevise)
                        <div class="mt-4">
                            @if (!$isEditingRevision)
                                <button wire:click="mulaiRevisi({{ $pengajuan->id }})"
                                    class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                                    Perbaiki Judul
                                </button>
                            @else
                                <div class="space-y-3 rounded-2xl border border-blue-200 bg-blue-50/50 p-4">
                                    <p class="text-sm font-semibold text-slate-800">Form Revisi Judul</p>

                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-slate-700">Judul
                                            Revisi</label>
                                        <input type="text" wire:model="revisiJudul.{{ $pengajuan->id }}"
                                            class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                                        @error('revisiJudul.' . $pengajuan->id)
                                            <x-ui.validation-error :message="$message" />
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-slate-700">Deskripsi
                                            Revisi</label>
                                        <textarea wire:model="revisiDeskripsi.{{ $pengajuan->id }}" rows="3"
                                            class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"></textarea>
                                        @error('revisiDeskripsi.' . $pengajuan->id)
                                            <x-ui.validation-error :message="$message" />
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-slate-700">Catatan Revisi ke
                                            Dosen</label>
                                        <textarea wire:model="revisiCatatan.{{ $pengajuan->id }}" rows="2"
                                            placeholder="Opsional: jelaskan apa yang sudah diperbaiki"
                                            class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"></textarea>
                                        @error('revisiCatatan.' . $pengajuan->id)
                                            <x-ui.validation-error :message="$message" />
                                        @enderror
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <button wire:click="kirimRevisi({{ $pengajuan->id }})"
                                            class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                                            Kirim Revisi Judul
                                        </button>
                                        <button wire:click="batalRevisi({{ $pengajuan->id }})"
                                            class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                            Batal
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div
                    class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                    Belum ada pengajuan judul.
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            <x-ui.show-entries wire:model.live="perPage" class="focus:border-blue-500 focus:ring-blue-500" />
        </div>

        <div class="mt-3">
            {{ $pengajuanList->links('vendor.pagination.tailwind') }}
        </div>
    </section>
</div>
