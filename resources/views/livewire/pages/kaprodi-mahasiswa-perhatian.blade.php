<div class="space-y-6">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-amber-900 to-rose-800 px-6 py-7 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-amber-100/80">Kaprodi</p>
                <h1 class="mt-2 text-2xl font-semibold sm:text-3xl">Mahasiswa Perlu Perhatian</h1>
                <p class="mt-2 max-w-2xl text-sm text-amber-100 sm:text-base">
                    Prioritaskan mahasiswa yang belum punya pembimbing, dokumennya bermasalah, atau sudah siap masuk tahap sidang.
                </p>
                <div
                    class="mt-3 inline-flex rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold text-amber-50 backdrop-blur">
                    Cakupan: {{ $canSeeAllProdi ? 'Semua program studi' : ($managedProdi?->name ?? 'Prodi belum ditentukan') }}
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-amber-100/70">Prioritas</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['total'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-amber-100/70">Tanpa Pembimbing</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['tanpa_pembimbing'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-amber-100/70">Dokumen Ditolak</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['dokumen_ditolak'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-amber-100/70">Siap Sidang</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['siap_belum_sidang'] }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Daftar Prioritas</h2>
                <p class="text-sm text-slate-500">Urutan berdasarkan tingkat urgensi akademik.</p>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label for="searchPerhatian" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Cari</label>
                    <input id="searchPerhatian" type="text" wire:model.live.debounce.350ms="search"
                        placeholder="Nama, NIM, prodi..."
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-amber-500 focus:ring-amber-500" />
                </div>
                <div>
                    <label for="kategoriPerhatian" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Kategori</label>
                    <select id="kategoriPerhatian" wire:model.live="kategori"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-amber-500 focus:ring-amber-500">
                        <option value="">Semua</option>
                        <option value="tanpa_pembimbing">Tanpa pembimbing</option>
                        <option value="dokumen_ditolak">Dokumen ditolak</option>
                        <option value="dokumen_pending">Dokumen menunggu review</option>
                        <option value="siap_belum_sidang">Siap belum sidang</option>
                    </select>
                </div>
                @if ($canSeeAllProdi)
                    <div>
                        <label for="prodiPerhatian" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Prodi</label>
                        <select id="prodiPerhatian" wire:model.live="prodiId"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-amber-500 focus:ring-amber-500">
                            <option value="">Semua</option>
                            @foreach ($prodiOptions as $prodi)
                                <option value="{{ $prodi->id }}">{{ $prodi->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-5 overflow-x-auto rounded-2xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Mahasiswa</th>
                        <th class="px-4 py-3">Prodi</th>
                        <th class="px-4 py-3">Fase</th>
                        <th class="px-4 py-3">Pembimbing</th>
                        <th class="px-4 py-3">Dokumen</th>
                        <th class="px-4 py-3">Alasan</th>
                        <th class="px-4 py-3">Skor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($rows as $row)
                        <tr wire:key="kaprodi-perhatian-{{ $row['id'] }}" class="hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $row['name'] }}</p>
                                <p class="text-xs text-slate-500">{{ $row['nim'] }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['prodi'] }}</td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-800">{{ $row['phase'] }}</p>
                                <div class="mt-1 h-2 min-w-28 rounded-full bg-slate-100">
                                    <div class="h-2 rounded-full bg-amber-500" style="width: {{ $row['progress'] }}%"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                @if ($row['supervisors']->isNotEmpty())
                                    {{ $row['supervisors']->implode(', ') }}
                                @else
                                    <span class="font-semibold text-amber-700">Belum ada</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $row['approved_documents'] }}/{{ $row['documents_count'] }} disetujui
                                @if ($row['pending_documents'] > 0)
                                    <p class="text-xs text-slate-500">{{ $row['pending_documents'] }} menunggu</p>
                                @endif
                                @if ($row['rejected_documents'] > 0)
                                    <p class="text-xs font-semibold text-rose-600">{{ $row['rejected_documents'] }} ditolak</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex max-w-sm flex-wrap gap-1.5">
                                    @foreach ($row['reasons'] as $reason)
                                        <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                            {{ $reason }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700">
                                    {{ $row['urgency_score'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">
                                Tidak ada mahasiswa prioritas sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
