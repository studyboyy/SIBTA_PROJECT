<div class="space-y-6">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-cyan-900 to-emerald-800 px-6 py-7 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-100/80">Kaprodi</p>
                <h1 class="mt-2 text-2xl font-semibold sm:text-3xl">Monitoring Mahasiswa Prodi</h1>
                <p class="mt-2 max-w-2xl text-sm text-cyan-100 sm:text-base">
                    Pantau status tugas akhir, pembimbing, dokumen, kelayakan sidang, dan jadwal mahasiswa.
                </p>
                <div
                    class="mt-3 inline-flex rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-50 backdrop-blur">
                    Cakupan: {{ $canSeeAllProdi ? 'Semua program studi' : ($managedProdi?->name ?? 'Prodi belum ditentukan') }}
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-cyan-100/70">Total Data</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $mahasiswaList->total() }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-cyan-100/70">Halaman Ini</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $mahasiswaList->count() }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-cyan-100/70">Per Halaman</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $perPage }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Daftar Mahasiswa</h2>
                <p class="text-sm text-slate-500">Filter berdasarkan status TA, fase progres, dan prodi.</p>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                <div class="sm:col-span-2">
                    <label for="searchMonitoring" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Cari</label>
                    <input id="searchMonitoring" type="text" wire:model.live.debounce.350ms="search"
                        placeholder="Nama, NIM, prodi, pembimbing..."
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-cyan-500 focus:ring-cyan-500" />
                </div>
                <div>
                    <label for="statusTaMonitoring" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Status TA</label>
                    <select id="statusTaMonitoring" wire:model.live="statusTa"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-cyan-500 focus:ring-cyan-500">
                        <option value="">Semua</option>
                        <option value="Pending">Pending</option>
                        <option value="Proses">Proses</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
                <div>
                    <label for="phaseMonitoring" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Fase</label>
                    <select id="phaseMonitoring" wire:model.live="phase"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-cyan-500 focus:ring-cyan-500">
                        <option value="">Semua</option>
                        @foreach ($phaseOptions as $phaseOption)
                            <option value="{{ $phaseOption }}">{{ $phaseOption }}</option>
                        @endforeach
                    </select>
                </div>
                @if ($canSeeAllProdi)
                    <div>
                        <label for="prodiMonitoring" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Prodi</label>
                        <select id="prodiMonitoring" wire:model.live="prodiId"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-cyan-500 focus:ring-cyan-500">
                            <option value="">Semua</option>
                            @foreach ($prodiOptions as $prodi)
                                <option value="{{ $prodi->id }}">{{ $prodi->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-5 flex items-center justify-between gap-3">
            <x-ui.show-entries wire:model.live="perPage" class="focus:border-cyan-500 focus:ring-cyan-500" />
        </div>

        <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Mahasiswa</th>
                        <th class="px-4 py-3">Prodi</th>
                        <th class="px-4 py-3">Pembimbing</th>
                        <th class="px-4 py-3">Status TA</th>
                        <th class="px-4 py-3">Fase</th>
                        <th class="px-4 py-3">Dokumen</th>
                        <th class="px-4 py-3">Sidang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($mahasiswaList as $mahasiswa)
                        @php
                            $row = $summaries->get($mahasiswa->id);
                            $supervisors = $row['supervisors'] ?? collect();
                        @endphp
                        <tr wire:key="kaprodi-monitoring-{{ $mahasiswa->id }}" class="hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $mahasiswa->user?->name ?? '-' }}</p>
                                <p class="text-xs text-slate-500">{{ $mahasiswa->nim }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $mahasiswa->programStudi?->name ?? $mahasiswa->prodi }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                @if ($supervisors->isNotEmpty())
                                    {{ $supervisors->implode(', ') }}
                                @else
                                    <span class="text-amber-700">Belum ada</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span @class([
                                    'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                    'bg-amber-100 text-amber-700' => $mahasiswa->status_ta === 'Pending',
                                    'bg-blue-100 text-blue-700' => $mahasiswa->status_ta === 'Proses',
                                    'bg-emerald-100 text-emerald-700' => $mahasiswa->status_ta === 'Selesai',
                                ])>
                                    {{ $mahasiswa->status_ta }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="min-w-32">
                                    <p class="font-medium text-slate-800">{{ $row['phase'] ?? '-' }}</p>
                                    <div class="mt-1 h-2 rounded-full bg-slate-100">
                                        <div class="h-2 rounded-full bg-cyan-500" style="width: {{ $row['progress'] ?? 0 }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $row['approved_documents'] ?? 0 }}/{{ $row['documents_count'] ?? 0 }} disetujui
                                @if (($row['rejected_documents'] ?? 0) > 0)
                                    <p class="text-xs font-semibold text-rose-600">{{ $row['rejected_documents'] }} ditolak</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                @if ($mahasiswa->sidang)
                                    {{ \Carbon\Carbon::parse($mahasiswa->sidang->jadwal)->translatedFormat('d M Y') }}
                                @elseif (($row['is_ready_for_sidang'] ?? false) === true)
                                    <span class="font-semibold text-emerald-700">Siap Sidang</span>
                                @else
                                    <span class="text-slate-400">Belum siap</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">
                                Belum ada mahasiswa sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $mahasiswaList->links('vendor.pagination.tailwind') }}
        </div>
    </section>
</div>
