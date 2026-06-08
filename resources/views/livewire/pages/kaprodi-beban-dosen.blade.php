<div class="space-y-6">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-emerald-900 to-cyan-800 px-6 py-7 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-emerald-100/80">Kaprodi</p>
                <h1 class="mt-2 text-2xl font-semibold sm:text-3xl">Beban Dosen Pembimbing</h1>
                <p class="mt-2 max-w-2xl text-sm text-emerald-100 sm:text-base">
                    Lihat distribusi bimbingan dosen untuk menjaga kuota tetap seimbang.
                </p>
                <div
                    class="mt-3 inline-flex rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold text-emerald-50 backdrop-blur">
                    Cakupan mahasiswa: {{ $canSeeAllProdi ? 'Semua program studi' : ($managedProdi?->name ?? 'Prodi belum ditentukan') }}
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-emerald-100/70">Dosen</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['total_dosen'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-emerald-100/70">Penuh</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['penuh'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-emerald-100/70">Beban Tinggi</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['tinggi'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-emerald-100/70">Longgar</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $summary['longgar'] }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Distribusi Pembimbing</h2>
                <p class="text-sm text-slate-500">Kuota dihitung dari total bimbingan aktif dosen secara keseluruhan.</p>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label for="searchBebanDosen" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Cari</label>
                    <input id="searchBebanDosen" type="text" wire:model.live.debounce.350ms="search"
                        placeholder="Nama atau NIDN..."
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" />
                </div>
                <div>
                    <label for="statusBebanDosen" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Status</label>
                    <select id="statusBebanDosen" wire:model.live="status"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Semua</option>
                        <option value="penuh">Penuh</option>
                        <option value="tinggi">Beban Tinggi</option>
                        <option value="longgar">Masih Longgar</option>
                    </select>
                </div>
                @if ($canSeeAllProdi)
                    <div>
                        <label for="prodiBebanDosen" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Prodi</label>
                        <select id="prodiBebanDosen" wire:model.live="prodiId"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:ring-emerald-500">
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
                        <th class="px-4 py-3">Dosen</th>
                        <th class="px-4 py-3">Mahasiswa Prodi</th>
                        <th class="px-4 py-3">Total Aktif</th>
                        <th class="px-4 py-3">Kuota</th>
                        <th class="px-4 py-3">Sisa</th>
                        <th class="px-4 py-3">Sesi</th>
                        <th class="px-4 py-3">Utilisasi</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($workloads as $row)
                        <tr wire:key="kaprodi-beban-dosen-{{ $row['id'] }}" class="hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $row['name'] }}</p>
                                <p class="text-xs text-slate-500">{{ $row['nidn'] ?: '-' }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['total_mahasiswa_prodi'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['total_mahasiswa_aktif'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['kuota'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $row['sisa'] }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $row['total_sesi'] }}
                                <p class="text-xs text-slate-400">{{ $row['total_hadir'] }} hadir</p>
                            </td>
                            <td class="px-4 py-3">
                                <div class="min-w-32">
                                    <div class="h-2 rounded-full bg-slate-100">
                                        <div @class([
                                            'h-2 rounded-full',
                                            'bg-rose-500' => $row['status_key'] === 'penuh',
                                            'bg-amber-500' => $row['status_key'] === 'tinggi',
                                            'bg-emerald-500' => $row['status_key'] === 'longgar',
                                        ]) style="width: {{ min($row['utilisasi'], 100) }}%"></div>
                                    </div>
                                    <p class="mt-1 text-xs text-slate-500">{{ $row['utilisasi'] }}%</p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span @class([
                                    'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                    'bg-rose-100 text-rose-700' => $row['status_key'] === 'penuh',
                                    'bg-amber-100 text-amber-700' => $row['status_key'] === 'tinggi',
                                    'bg-emerald-100 text-emerald-700' => $row['status_key'] === 'longgar',
                                ])>
                                    {{ $row['status_label'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500">
                                Belum ada data dosen sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
