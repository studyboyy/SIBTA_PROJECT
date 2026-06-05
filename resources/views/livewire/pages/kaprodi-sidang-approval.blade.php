<div class="space-y-6">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-blue-900 to-cyan-800 px-6 py-7 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-100/80">Modul Kaprodi</p>
                <h1 class="mt-2 text-2xl font-semibold sm:text-3xl">Approval Sidang Program Studi</h1>
                <p class="mt-2 max-w-2xl text-sm text-cyan-100 sm:text-base">Tinjau pengajuan sidang mahasiswa untuk
                    prodi {{ $managedProdi?->name ?? 'yang diampu' }} sebelum diproses admin.</p>
            </div>
            <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                <p class="text-xs uppercase tracking-wide text-cyan-100/70">Prodi</p>
                <p class="mt-2 text-xl font-semibold">{{ $managedProdi?->name ?? 'Belum ditetapkan' }}</p>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="w-full sm:max-w-sm">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Pencarian</label>
                <input type="text" wire:model.live.debounce.350ms="search" placeholder="Cari nama atau NIM..."
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500" />
            </div>
            <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-end">
                <div class="sm:w-56"><label
                        class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Status
                        Kaprodi</label><select wire:model.live="statusFilter"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select></div>
                <div class="sm:w-40"><x-ui.show-entries wire:model.live="perPage"
                        class="focus:border-blue-500 focus:ring-blue-500" /></div>
            </div>
        </div>

        <div class="mt-5 space-y-4">
            @forelse ($pengajuanList as $pengajuan)
                <article class="rounded-2xl border border-slate-200 p-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="font-semibold text-slate-900">
                                {{ $pengajuan->mahasiswa?->user?->name ?? 'Mahasiswa' }}</p>
                            <p class="text-sm text-slate-500">{{ $pengajuan->mahasiswa?->nim ?? '-' }} •
                                {{ $pengajuan->mahasiswa?->programStudi?->name ?? ($pengajuan->mahasiswa?->prodi ?? '-') }}
                            </p>
                            <p class="mt-1 text-xs text-slate-400">Diajukan
                                {{ $pengajuan->diajukan_pada?->translatedFormat('d M Y H:i') ?? '-' }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2 text-xs font-semibold">
                            <span
                                class="rounded-full px-3 py-1 {{ ($pengajuan->status_dosen ?? 'pending') === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">Dosen:
                                {{ ucfirst($pengajuan->status_dosen ?? 'pending') }}</span>
                            <span
                                class="rounded-full px-3 py-1 {{ ($pengajuan->status_kaprodi ?? 'pending') === 'approved' ? 'bg-emerald-100 text-emerald-700' : (($pengajuan->status_kaprodi ?? 'pending') === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700') }}">Kaprodi:
                                {{ ucfirst($pengajuan->status_kaprodi ?? 'pending') }}</span>
                            <span
                                class="rounded-full px-3 py-1 {{ ($pengajuan->status ?? 'pending') === 'approved' ? 'bg-blue-100 text-blue-700' : (($pengajuan->status ?? 'pending') === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700') }}">Admin:
                                {{ ucfirst($pengajuan->status ?? 'pending') }}</span>
                        </div>
                    </div>

                    @if ($pengajuan->catatan_mahasiswa)
                        <div class="mt-3 rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-700"><span
                                class="font-semibold text-slate-900">Catatan mahasiswa:</span>
                            {{ $pengajuan->catatan_mahasiswa }}</div>
                    @endif

                    @if ($pengajuan->catatan_kaprodi)
                        <div class="mt-3 rounded-xl bg-blue-50 px-4 py-3 text-sm text-blue-800"><span
                                class="font-semibold">Catatan kaprodi:</span> {{ $pengajuan->catatan_kaprodi }}</div>
                    @endif

                    @if (($pengajuan->status ?? 'pending') === 'approved')
                        <div class="mt-3 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-800">Pengajuan sudah
                            diproses admin dan masuk ke gelombang {{ $pengajuan->gelombang ?? '-' }}.</div>
                    @elseif (($pengajuan->status_kaprodi ?? 'pending') === 'pending')
                        {{-- Kaprodi hanya bisa approve jika dosen sudah acc kelayakan --}}
                        @if (($pengajuan->status_dosen ?? 'pending') !== 'approved')
                            <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                                Menunggu ACC kelayakan dari dosen pembimbing sebelum kaprodi dapat menyetujui.
                            </div>
                        @else
                            <div class="mt-3 flex flex-wrap gap-2">
                                <button wire:click="confirmAction({{ $pengajuan->id }}, 'approved')"
                                    class="rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700">Approve
                                    Kaprodi</button>
                                <button wire:click="confirmAction({{ $pengajuan->id }}, 'rejected')"
                                    class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-700">Tolak</button>
                            </div>
                        @endif
                    @elseif (($pengajuan->status_kaprodi ?? '') === 'rejected')
                        <div class="mt-3 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-800">Pengajuan sudah ditolak kaprodi.</div>
                    @elseif (($pengajuan->status_kaprodi ?? '') === 'approved')
                        <div class="mt-3 rounded-xl bg-blue-50 px-4 py-3 text-sm text-blue-800">Sudah disetujui kaprodi, menunggu proses admin.</div>
                    @endif
                </article>
            @empty
                <div
                    class="rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-sm text-slate-500">
                    Belum ada pengajuan sidang untuk prodi ini.</div>
            @endforelse
        </div>

        <div class="mt-4">{{ $pengajuanList->links('vendor.pagination.tailwind') }}</div>
    </section>

    <livewire:components.modal name="kaprodi-sidang-action">
        <div class="w-full">
            <h3 class="text-center text-lg font-semibold text-slate-900">
                {{ $actionType === 'rejected' ? 'Tolak Pengajuan Sidang' : 'Approve Pengajuan Sidang' }}</h3>
            <p class="mt-2 text-center text-sm text-slate-600">Tambahkan catatan jika diperlukan sebelum keputusan
                dikirim.</p>
            <div class="mt-4"><label class="block text-sm font-medium text-slate-700">Catatan Kaprodi</label>
                <textarea wire:model.defer="catatan_kaprodi" rows="4"
                    class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('catatan_kaprodi')
                    <x-ui.validation-error :message="$message" />
                @enderror
            </div>
            <div class="mt-5 grid grid-cols-1 gap-2 sm:grid-cols-2">
                <button type="button" wire:click="$dispatch('close-modal', {name: 'kaprodi-sidang-action'})"
                    class="inline-flex w-full justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</button>
                <button type="button" wire:click="submitAction"
                    class="inline-flex w-full justify-center rounded-xl {{ $actionType === 'rejected' ? 'bg-rose-600 hover:bg-rose-500' : 'bg-blue-600 hover:bg-blue-500' }} px-4 py-2 text-sm font-semibold text-white transition">{{ $actionType === 'rejected' ? 'Tolak Pengajuan' : 'Approve Pengajuan' }}</button>
            </div>
        </div>
    </livewire:components.modal>
</div>
