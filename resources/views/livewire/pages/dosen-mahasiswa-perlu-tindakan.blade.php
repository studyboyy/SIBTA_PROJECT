<div class="space-y-6">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-amber-900 to-blue-800 px-6 py-7 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-amber-100/80">Panel Dosen</p>
                <h1 class="mt-2 text-2xl font-semibold sm:text-3xl">Mahasiswa Perlu Tindakan</h1>
                <p class="mt-2 max-w-2xl text-sm text-amber-100 sm:text-base">
                    Prioritaskan mahasiswa bimbingan yang dokumennya menunggu review, perlu revisi, atau lama tidak bimbingan.
                </p>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-amber-100/70">Prioritas</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['total'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-amber-100/70">Pending</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['dokumen_pending'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-amber-100/70">Ditolak</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['dokumen_ditolak'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-amber-100/70">Sidang</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['sidang_pending'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-amber-100/70">Bimbingan</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['lama_tidak_bimbingan'] }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Daftar Prioritas</h2>
                <p class="text-sm text-slate-500">Urutan berdasarkan skor tindakan dosen.</p>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="searchTindakanDosen" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Cari</label>
                    <input id="searchTindakanDosen" type="text" wire:model.live.debounce.350ms="search"
                        placeholder="Nama, NIM, prodi..."
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-amber-500 focus:ring-amber-500" />
                </div>
                <div>
                    <label for="kategoriTindakanDosen" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Kategori</label>
                    <select id="kategoriTindakanDosen" wire:model.live="kategori"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-amber-500 focus:ring-amber-500">
                        <option value="">Semua</option>
                        <option value="dokumen_pending">Dokumen pending</option>
                        <option value="dokumen_ditolak">Dokumen ditolak</option>
                        <option value="sidang_pending">Kelayakan sidang</option>
                        <option value="lama_tidak_bimbingan">Lama tidak bimbingan</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mt-5 overflow-x-auto rounded-2xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Mahasiswa</th>
                        <th class="px-4 py-3">Fase</th>
                        <th class="px-4 py-3">Dokumen</th>
                        <th class="px-4 py-3">Bimbingan Terakhir</th>
                        <th class="px-4 py-3">Alasan</th>
                        <th class="px-4 py-3">Skor</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($rows as $row)
                        <tr wire:key="dosen-tindakan-{{ $row['id'] }}" class="hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $row['name'] }}</p>
                                <p class="text-xs text-slate-500">{{ $row['nim'] }} · {{ $row['prodi'] }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-800">{{ $row['phase'] }}</p>
                                <div class="mt-1 h-2 min-w-28 rounded-full bg-slate-100">
                                    <div class="h-2 rounded-full bg-amber-500" style="width: {{ $row['progress'] }}%"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $row['approved_documents'] }}/{{ $row['documents_count'] }} disetujui
                                @if ($row['pending_documents'] > 0)
                                    <p class="text-xs text-amber-700">{{ $row['pending_documents'] }} pending</p>
                                @endif
                                @if ($row['rejected_documents'] > 0)
                                    <p class="text-xs font-semibold text-rose-600">{{ $row['rejected_documents'] }} ditolak</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $row['latest_bimbingan'] ? \Carbon\Carbon::parse($row['latest_bimbingan'])->translatedFormat('d M Y') : 'Belum ada' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex max-w-md flex-wrap gap-1.5">
                                    @foreach ($row['action_reasons'] as $reason)
                                        <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                            {{ $reason }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700">
                                    {{ $row['action_score'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @if ($row['pending_documents'] > 0 || $row['rejected_documents'] > 0)
                                        <a href="{{ route('dosen.review-dokumen') }}" wire:navigate
                                            class="rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                                            Dokumen
                                        </a>
                                    @endif
                                    @if ($row['sidang_action_pending'])
                                        <a href="{{ route('dosen.kelayakan-sidang') }}" wire:navigate
                                            class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100">
                                            Sidang
                                        </a>
                                    @endif
                                    @if ($row['long_without_guidance'])
                                        <a href="{{ route('dosen.bimbingan') }}" wire:navigate
                                            class="rounded-lg bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-100">
                                            Jadwalkan
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">
                                Tidak ada mahasiswa yang perlu tindakan sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
