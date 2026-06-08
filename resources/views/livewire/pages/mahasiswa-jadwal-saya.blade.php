<div class="space-y-6">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-indigo-900 to-emerald-800 px-6 py-7 text-white shadow-lg sm:px-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-indigo-100/80">Portal Mahasiswa</p>
                <h1 class="mt-2 text-2xl font-semibold sm:text-3xl">Jadwal Saya</h1>
                <p class="mt-2 max-w-2xl text-sm text-indigo-100 sm:text-base">
                    Gabungan agenda bimbingan dan jadwal sidang dalam satu tempat.
                </p>
            </div>
            <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                <p class="text-xs uppercase tracking-wide text-indigo-100/70">Agenda Terdekat</p>
                <p class="mt-2 text-lg font-semibold">{{ $nextAgenda['title'] ?? 'Belum ada' }}</p>
                @if ($nextAgenda)
                    <p class="mt-1 text-xs text-indigo-100">{{ \Carbon\Carbon::parse($nextAgenda['date'])->translatedFormat('d M Y') }}</p>
                @endif
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Agenda</h2>
                <p class="text-sm text-slate-500">Filter agenda mendatang atau riwayat yang sudah lewat.</p>
            </div>
            <div>
                <label for="filterJadwalSaya" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Filter</label>
                <select id="filterJadwalSaya" wire:model.live="filter"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    <option value="upcoming">Mendatang</option>
                    <option value="past">Riwayat</option>
                </select>
            </div>
        </div>

        <div class="mt-5 overflow-x-auto rounded-2xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Agenda</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Tempat/Link</th>
                        <th class="px-4 py-3">Dosen/Penguji</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($agenda as $item)
                        <tr wire:key="mahasiswa-agenda-{{ $loop->index }}" class="hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $item['title'] }}</p>
                                <p class="text-xs text-slate-500">{{ ucfirst($item['type']) }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $item['date'] ? \Carbon\Carbon::parse($item['date'])->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $item['time'] ?: '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                @if ($item['type'] === 'bimbingan' && str_starts_with((string) $item['location'], 'http'))
                                    <a href="{{ $item['location'] }}" target="_blank" class="font-semibold text-indigo-700 hover:text-indigo-900">Buka link</a>
                                @else
                                    {{ $item['location'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                @forelse ($item['people'] as $person)
                                    <p>{{ $person }}</p>
                                @empty
                                    -
                                @endforelse
                            </td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                    {{ ucfirst($item['status']) }}
                                </span>
                                @if (($item['confirmation'] ?? null) !== null)
                                    <p class="mt-1 text-xs text-slate-500">Konfirmasi: {{ ucfirst($item['confirmation']) }}</p>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">
                                Belum ada agenda sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
