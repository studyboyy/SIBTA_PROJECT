<div class="space-y-6">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-indigo-900 to-cyan-800 px-6 py-7 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-indigo-100/80">Panel Dosen</p>
                <h1 class="mt-2 text-2xl font-semibold sm:text-3xl">Monitoring Mahasiswa Bimbingan</h1>
                <p class="mt-2 max-w-2xl text-sm text-indigo-100 sm:text-base">
                    Pantau progres, dokumen, judul, bimbingan terakhir, dan kesiapan sidang mahasiswa bimbingan.
                </p>
                <div
                    class="mt-3 inline-flex rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold text-indigo-50 backdrop-blur">
                    Dosen: {{ $dosen->user?->name ?? '-' }}
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-indigo-100/70">Total Data</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $mahasiswaList->total() }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-indigo-100/70">Halaman Ini</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $mahasiswaList->count() }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-indigo-100/70">Per Halaman</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $perPage }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Daftar Mahasiswa</h2>
                <p class="text-sm text-slate-500">Filter berdasarkan nama, status TA, atau status kelayakan sidang.</p>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label for="searchMonitoringDosen" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Cari</label>
                    <input id="searchMonitoringDosen" type="text" wire:model.live.debounce.350ms="search"
                        placeholder="Nama, NIM, judul..."
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:ring-indigo-500" />
                </div>
                <div>
                    <label for="statusTaMonitoringDosen" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Status TA</label>
                    <select id="statusTaMonitoringDosen" wire:model.live="statusTa"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua</option>
                        <option value="Pending">Pending</option>
                        <option value="Proses">Proses</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
                <div>
                    <label for="sidangStatusMonitoringDosen" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Kelayakan Sidang</label>
                    <select id="sidangStatusMonitoringDosen" wire:model.live="sidangStatus"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua</option>
                        <option value="belum_ajukan">Belum ajukan</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="revisi">Revisi</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <x-ui.show-entries wire:model.live="perPage" class="focus:border-indigo-500 focus:ring-indigo-500" />
        </div>

        <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Mahasiswa</th>
                        <th class="px-4 py-3">Judul Terakhir</th>
                        <th class="px-4 py-3">Status TA</th>
                        <th class="px-4 py-3">Progres</th>
                        <th class="px-4 py-3">Dokumen</th>
                        <th class="px-4 py-3">Bimbingan</th>
                        <th class="px-4 py-3">Sidang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($mahasiswaList as $mahasiswa)
                        @php
                            $row = $summaries->get($mahasiswa->id);
                            $latestJudul = $mahasiswa->pengajuanJuduls->first();
                            $latestBimbingan = $mahasiswa->bimbinganLogs->first();
                            $sidangStatus = $mahasiswa->pengajuanSidang?->status_dosen ?? 'belum_ajukan';
                        @endphp
                        <tr wire:key="dosen-monitoring-{{ $mahasiswa->id }}" class="hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $mahasiswa->user?->name ?? '-' }}</p>
                                <p class="text-xs text-slate-500">{{ $mahasiswa->nim }} · {{ $mahasiswa->programStudi?->name ?? $mahasiswa->prodi }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="max-w-xs truncate font-medium text-slate-800">{{ $latestJudul?->judul ?? '-' }}</p>
                                <p class="text-xs text-slate-500">{{ $latestJudul?->status ? ucfirst($latestJudul->status) : 'Belum ada pengajuan' }}</p>
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
                                        <div class="h-2 rounded-full bg-indigo-500" style="width: {{ $row['progress'] ?? 0 }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $row['approved_documents'] ?? 0 }}/{{ $row['documents_count'] ?? 0 }} disetujui
                                @if (($row['pending_documents'] ?? 0) > 0)
                                    <p class="text-xs text-amber-700">{{ $row['pending_documents'] }} menunggu</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $row['bimbingan_count'] ?? 0 }} sesi
                                <p class="text-xs text-slate-500">
                                    {{ $latestBimbingan?->tanggal ? \Carbon\Carbon::parse($latestBimbingan->tanggal)->translatedFormat('d M Y') : 'Belum ada sesi' }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <span @class([
                                    'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                    'bg-slate-100 text-slate-600' => $sidangStatus === 'belum_ajukan',
                                    'bg-amber-100 text-amber-700' => $sidangStatus === 'pending',
                                    'bg-emerald-100 text-emerald-700' => $sidangStatus === 'approved',
                                    'bg-orange-100 text-orange-700' => $sidangStatus === 'revisi',
                                    'bg-rose-100 text-rose-700' => $sidangStatus === 'rejected',
                                ])>
                                    {{ $sidangStatus === 'belum_ajukan' ? 'Belum ajukan' : ucfirst($sidangStatus) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">
                                Belum ada mahasiswa bimbingan sesuai filter.
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
