<div class="space-y-8">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-indigo-900 to-cyan-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-cyan-100/80">Panel Dosen SIBTA</p>
            <h1 class="text-3xl font-semibold sm:text-4xl">Review Pengajuan Judul</h1>
            <p class="max-w-2xl text-sm text-cyan-100 sm:text-base">
                Verifikasi pengajuan judul mahasiswa bimbingan Anda, beri catatan perbaikan, dan tentukan status.
            </p>
        </div>
    </section>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Daftar Pengajuan</h2>
                <p class="text-sm text-slate-500">Pengajuan judul dari mahasiswa yang Anda bimbing.</p>
                <p class="text-xs text-slate-400">Dosen: {{ $dosen->user?->name ?? '-' }}
                    @if ($mahasiswaCount > 0)
                        • {{ $mahasiswaCount }} mahasiswa bimbingan
                    @else
                        • <span class="text-amber-600">Belum ada mahasiswa bimbingan yang ditetapkan</span>
                    @endif
                </p>
            </div>
            <div class="grid w-full gap-3 sm:grid-cols-2 lg:max-w-xl">
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama, NIM, judul, atau catatan"
                    class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                <select wire:model.live="status"
                    class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="">Semua status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="revisi">Revisi</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>

        <div class="mt-6 space-y-4">
            @if ($mahasiswaCount === 0)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-6 text-center text-sm text-amber-800">
                    <p class="font-semibold">Belum ada mahasiswa bimbingan</p>
                    <p class="mt-1 text-xs text-amber-700">Anda akan dapat mereview pengajuan judul setelah admin atau kaprodi menetapkan Anda sebagai dosen pembimbing mahasiswa.</p>
                </div>
            @else
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
                <article class="rounded-2xl border border-slate-200 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $pengajuan->judul }}</p>
                            <p class="mt-1 text-sm text-slate-500">
                                {{ $pengajuan->mahasiswa?->user?->name ?? 'Mahasiswa' }}
                                • {{ $pengajuan->mahasiswa?->nim ?? '-' }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                Diajukan {{ $pengajuan->created_at?->translatedFormat('d M Y H:i') }}</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                            {{ ucfirst($pengajuan->status ?? 'pending') }}
                        </span>
                    </div>

                    @if ($pengajuan->deskripsi)
                        <p class="mt-3 text-sm text-slate-600">{{ $pengajuan->deskripsi }}</p>
                    @endif

                    @if ($pengajuan->calonDosenPembimbing)
                        <div class="mt-3 rounded-xl bg-indigo-50 px-3 py-2 text-sm text-indigo-800">
                            <span class="font-medium text-indigo-900">Calon Dosen Pembimbing Pilihan Mahasiswa:</span>
                            {{ $pengajuan->calonDosenPembimbing->user->name ?? '-' }}
                            ({{ $pengajuan->calonDosenPembimbing->nidn }})
                        </div>
                    @endif

                    @if (($pengajuan->revisi_ke ?? 0) > 0)
                        <div class="mt-3 rounded-xl bg-blue-50 px-3 py-2 text-sm text-blue-700">
                            <span class="font-medium text-blue-800">Revisi Mahasiswa:</span>
                            Revisi ke-{{ $pengajuan->revisi_ke }} dikirim
                            {{ $pengajuan->revisi_dikirim_pada?->translatedFormat('d M Y H:i') ?? '-' }}
                            @if ($pengajuan->catatan_revisi_mahasiswa)
                                <span class="block mt-1">Catatan mahasiswa:
                                    {{ $pengajuan->catatan_revisi_mahasiswa }}</span>
                            @endif
                        </div>
                    @endif

                    <div class="mt-4 rounded-xl bg-slate-50 px-3 py-2 text-sm text-slate-600">
                        <span class="font-medium text-slate-700">Catatan Saat Ini:</span>
                        {{ $pengajuan->catatan ?: 'Belum ada catatan.' }}
                    </div>

                    @php
                        $isApproved = in_array($status, ['approved', 'disetujui'], true);
                        $isEditingStatus = in_array($pengajuan->id, $editingStatusIds ?? [], true);
                    @endphp

                    <div class="mt-4 space-y-3">
                        @if ($isApproved && !$isEditingStatus)
                            <button wire:click="mulaiEditStatus({{ $pengajuan->id }})"
                                class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                                Edit Status
                            </button>
                        @else
                            <textarea wire:model="catatan.{{ $pengajuan->id }}" rows="2"
                                placeholder="Catatan review (wajib untuk revisi/rejected)"
                                class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"></textarea>
                            @error('catatan.' . $pengajuan->id)
                                <x-ui.validation-error :message="$message" />
                            @enderror

                            <div class="flex flex-wrap gap-2">
                                <button wire:click="updateStatus({{ $pengajuan->id }}, 'approved')"
                                    class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">
                                    Approve
                                </button>
                                <button wire:click="updateStatus({{ $pengajuan->id }}, 'revisi')"
                                    class="rounded-lg bg-orange-500 px-3 py-2 text-xs font-semibold text-white hover:bg-orange-600">
                                    Minta Revisi
                                </button>
                                <button wire:click="updateStatus({{ $pengajuan->id }}, 'rejected')"
                                    class="rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">
                                    Reject
                                </button>
                                @if ($isApproved && $isEditingStatus)
                                    <button wire:click="batalEditStatus({{ $pengajuan->id }})"
                                        class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        Batal
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </article>
            @empty
                <div
                    class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                    Belum ada data pengajuan judul.
                </div>
            @endforelse
            @endif
        </div>

        <div class="mt-6">
            <x-ui.show-entries wire:model.live="perPage" class="focus:border-blue-500 focus:ring-blue-500" />
        </div>

        <div class="mt-3">
            {{ $pengajuanList->links('vendor.pagination.tailwind') }}
        </div>
    </section>
</div>
