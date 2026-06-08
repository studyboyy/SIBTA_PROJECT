<div class="space-y-6">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-emerald-900 to-blue-800 px-6 py-7 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-emerald-100/80">Panel Dosen</p>
                <h1 class="mt-2 text-2xl font-semibold sm:text-3xl">Kelayakan Sidang</h1>
                <p class="mt-2 max-w-2xl text-sm text-emerald-100 sm:text-base">
                    Tinjau checklist dokumen dan berikan ACC kelayakan sidang untuk mahasiswa bimbingan.
                </p>
                <div
                    class="mt-3 inline-flex rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold text-emerald-50 backdrop-blur">
                    Dosen: {{ $dosen->user?->name ?? '-' }}
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-emerald-100/70">Total Data</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $pengajuanList->total() }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-emerald-100/70">Halaman Ini</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $pengajuanList->count() }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-emerald-100/70">Per Halaman</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $perPage }}</p>
                </div>
            </div>
        </div>
    </section>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
            {{ session('error') }}
        </div>
    @endif

    <section class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Pengajuan Sidang</h2>
                <p class="text-sm text-slate-500">Mahasiswa yang sudah mengirim pengajuan sidang akan muncul di sini.</p>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="searchKelayakanSidang" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Cari</label>
                    <input id="searchKelayakanSidang" type="text" wire:model.live.debounce.350ms="search"
                        placeholder="Nama, NIM, catatan..."
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" />
                </div>
                <div>
                    <label for="statusKelayakanSidang" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Status</label>
                    <select id="statusKelayakanSidang" wire:model.live="status"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Semua</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="revisi">Revisi</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <x-ui.show-entries wire:model.live="perPage" class="focus:border-emerald-500 focus:ring-emerald-500" />
        </div>

        <div class="mt-4 space-y-3">
            @forelse ($pengajuanList as $pengajuan)
                @php
                    $checklist = $checklists->get($pengajuan->id, []);
                    $approvedRequired = count(array_filter($checklist));
                    $requiredTotal = count($requiredTypes);
                    $isComplete = $approvedRequired === $requiredTotal;
                @endphp
                <article wire:key="dosen-kelayakan-{{ $pengajuan->id }}" class="rounded-2xl border border-slate-200 p-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $pengajuan->mahasiswa?->user?->name ?? 'Mahasiswa' }}</p>
                            <p class="text-xs text-slate-500">
                                {{ $pengajuan->mahasiswa?->nim ?? '-' }} · {{ $pengajuan->mahasiswa?->programStudi?->name ?? $pengajuan->mahasiswa?->prodi }}
                            </p>
                            <p class="mt-2 text-xs text-slate-500">
                                Diajukan: {{ $pengajuan->diajukan_pada?->translatedFormat('d M Y H:i') ?? '-' }}
                            </p>
                        </div>
                        <span @class([
                            'inline-flex w-fit rounded-full px-2.5 py-1 text-xs font-semibold',
                            'bg-amber-100 text-amber-700' => $pengajuan->status_dosen === 'pending',
                            'bg-emerald-100 text-emerald-700' => $pengajuan->status_dosen === 'approved',
                            'bg-orange-100 text-orange-700' => $pengajuan->status_dosen === 'revisi',
                            'bg-rose-100 text-rose-700' => $pengajuan->status_dosen === 'rejected',
                        ])>
                            {{ ucfirst($pengajuan->status_dosen ?? 'pending') }}
                        </span>
                    </div>

                    <div class="mt-4 grid gap-3 lg:grid-cols-[1fr_1fr]">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Checklist Dokumen Wajib</p>
                            <div class="mt-2 space-y-2">
                                @foreach ($requiredTypes as $type)
                                    <div class="flex items-center justify-between gap-3 rounded-lg bg-white px-3 py-2 text-sm">
                                        <span class="text-slate-700">{{ $documentLabels[$type] ?? ucfirst($type) }}</span>
                                        <span @class([
                                            'rounded-full px-2.5 py-1 text-xs font-semibold',
                                            'bg-emerald-100 text-emerald-700' => $checklist[$type] ?? false,
                                            'bg-amber-100 text-amber-700' => ! ($checklist[$type] ?? false),
                                        ])>
                                            {{ ($checklist[$type] ?? false) ? 'Lengkap' : 'Belum' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            <p class="mt-2 text-xs text-slate-500">
                                {{ $approvedRequired }}/{{ $requiredTotal }} dokumen wajib disetujui.
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-white p-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Catatan Pengajuan</p>
                            <p class="mt-2 text-sm text-slate-700">{{ $pengajuan->catatan_mahasiswa ?: '-' }}</p>
                            <p class="mt-3 text-xs text-slate-500">Status kaprodi: {{ ucfirst($pengajuan->status_kaprodi ?? 'pending') }}</p>
                            <p class="mt-1 text-xs text-slate-500">Status admin: {{ ucfirst($pengajuan->status ?? 'pending') }}</p>
                        </div>
                    </div>

                    <textarea wire:model="catatanSidang.{{ $pengajuan->id }}" rows="2"
                        placeholder="Catatan dosen untuk kelayakan sidang"
                        class="mt-4 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                    @error('catatanSidang.' . $pengajuan->id)
                        <x-ui.validation-error :message="$message" />
                    @enderror

                    @if ($pengajuan->status_dosen === 'approved' && $pengajuan->acc_kelayakan_at)
                        <div class="mt-3 rounded-xl border border-blue-200 bg-blue-50 px-3 py-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Tanda Tangan Digital</p>
                            <p class="mt-1 text-xs text-blue-800">
                                SIG-SDG-{{ $this->buildDigitalSignature('sidang', $pengajuan->id, $pengajuan->acc_kelayakan_at, $dosen->user_id) }}
                            </p>
                            <p class="mt-1 text-[11px] text-blue-700/90">
                                Ditandatangani oleh {{ $dosen->user?->name ?? 'Dosen' }} pada
                                {{ $pengajuan->acc_kelayakan_at?->translatedFormat('d M Y H:i') }}
                            </p>
                        </div>
                    @endif

                    <div class="mt-3 flex flex-wrap gap-2">
                        <button type="button" wire:click="updateSidangStatus({{ $pengajuan->id }}, 'approved')"
                            @disabled(! $isComplete || $pengajuan->status_dosen === 'approved')
                            class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50">
                            ACC Layak Sidang
                        </button>
                        <button type="button" wire:click="updateSidangStatus({{ $pengajuan->id }}, 'revisi')"
                            class="rounded-lg bg-orange-500 px-3 py-2 text-xs font-semibold text-white hover:bg-orange-600">
                            Revisi
                        </button>
                        <button type="button" wire:click="updateSidangStatus({{ $pengajuan->id }}, 'rejected')"
                            class="rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-700">
                            Reject
                        </button>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-sm text-slate-500">
                    Belum ada pengajuan sidang sesuai filter.
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $pengajuanList->links('vendor.pagination.tailwind') }}
        </div>
    </section>
</div>
