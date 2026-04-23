<div class="space-y-8">
    {{-- HEADER --}}
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-indigo-900 to-blue-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="space-y-2">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-indigo-100/80">Portal Dosen</p>
            <h1 class="text-3xl font-semibold sm:text-4xl">Penjadwalan Bimbingan</h1>
            <p class="max-w-2xl text-sm text-indigo-100 sm:text-base">
                Atur jadwal sesi bimbingan dan kelola pelaksanaan sesi dengan mahasiswa bimbingan Anda.
            </p>
        </div>
    </section>

    {{-- FLASH --}}
    @if (session('success'))
        <div class="rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700 border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-8 xl:grid-cols-[380px_1fr]">

        {{-- FORM TAMBAH / EDIT --}}
        <aside class="space-y-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-base font-semibold text-slate-900">
                    {{ $editId ? 'Edit Penjadwalan Bimbingan' : 'Tambah Penjadwalan Bimbingan' }}
                </h2>

                <div class="space-y-4">
                    {{-- Mahasiswa Target --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Mahasiswa Terkait</label>
                        @if ($mahasiswas->isEmpty())
                            <p class="rounded-xl border border-dashed border-slate-200 p-3 text-sm text-slate-500">
                                Belum ada mahasiswa bimbingan.
                            </p>
                        @else
                            @if ($editId)
                                @php
                                    $selectedMahasiswa =
                                        $mahasiswas->firstWhere('id', (int) $mahasiswa_id) ?? $mahasiswas->first();
                                @endphp
                                <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                                    <p class="font-semibold text-slate-800">
                                        {{ $selectedMahasiswa?->user?->name ?? '-' }}
                                    </p>
                                    <p class="text-xs text-slate-500">{{ $selectedMahasiswa?->nim ?? '-' }}</p>
                                </div>
                            @else
                                <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2.5 text-sm">
                                    <p class="font-semibold text-indigo-800">Jadwal akan dibuat untuk semua mahasiswa
                                        bimbingan</p>
                                    <p class="text-xs text-indigo-700">Total target: {{ $mahasiswas->count() }}
                                        mahasiswa</p>
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Tanggal --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Tanggal Bimbingan</label>
                        <input type="date" wire:model="tanggal"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100" />
                        @error('tanggal')
                            <x-ui.validation-error :message="$message" />
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Jam Bimbingan</label>
                        <input type="time" wire:model="jam"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100" />
                        @error('jam')
                            <x-ui.validation-error :message="$message" />
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Mode Bimbingan</label>
                        <select wire:model.live="mode"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                            <option value="offline">Offline</option>
                            <option value="online">Online</option>
                        </select>
                        @error('mode')
                            <x-ui.validation-error :message="$message" />
                        @enderror
                    </div>

                    @if ($mode === 'offline')
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Lokasi</label>
                            <input type="text" wire:model="lokasi" placeholder="Contoh: Ruang Dosen 2"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100" />
                            @error('lokasi')
                                <x-ui.validation-error :message="$message" />
                            @enderror
                        </div>
                    @else
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Link Meeting</label>
                            <input type="url" wire:model="link_online" placeholder="https://..."
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100" />
                            @error('link_online')
                                <x-ui.validation-error :message="$message" />
                            @enderror
                        </div>
                    @endif

                    {{-- Catatan --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Agenda / Catatan Sesi</label>
                        <textarea wire:model="catatan" rows="4"
                            placeholder="Tuliskan agenda bimbingan atau catatan yang perlu disiapkan..."
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100 resize-none"></textarea>
                        @error('catatan')
                            <x-ui.validation-error :message="$message" />
                        @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-2 pt-1">
                        <button wire:click="simpan" wire:loading.attr="disabled"
                            class="flex-1 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-60">
                            <span wire:loading.remove wire:target="simpan">
                                {{ $editId ? 'Simpan Perubahan' : 'Buat Jadwal ke Semua Mahasiswa' }}
                            </span>
                            <span wire:loading wire:target="simpan">Menyimpan...</span>
                        </button>
                        @if ($editId)
                            <button wire:click="resetForm" type="button"
                                class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                                Batal
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Mini stats --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Total Mahasiswa Bimbingan</p>
                <p class="mt-1 text-3xl font-semibold text-slate-900">{{ $mahasiswas->count() }}</p>
                <p class="mt-3 text-sm text-slate-500">Total Jadwal Bimbingan</p>
                <p class="mt-1 text-3xl font-semibold text-slate-900">{{ $logs->total() }}</p>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-900">Progress Kehadiran Mahasiswa</h3>
                <div class="mt-3 space-y-3">
                    @forelse ($progressByMahasiswa as $item)
                        <div class="rounded-xl border border-slate-200 px-3 py-2">
                            <p class="text-sm font-semibold text-slate-800">{{ $item['name'] }}</p>
                            <p class="text-xs text-slate-500">{{ $item['nim'] }} •
                                {{ $item['hadir'] }}/{{ $item['total'] }} hadir</p>
                            <div class="mt-2 h-2 rounded-full bg-slate-100">
                                <div class="h-2 rounded-full bg-indigo-500"
                                    style="width: {{ max($item['progress'], 5) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada data progress.</p>
                    @endforelse
                </div>
            </div>
        </aside>

        {{-- DAFTAR LOG --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-base font-semibold text-slate-900">Daftar Penjadwalan</h2>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cari agenda / nama / NIM..."
                        class="rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100 w-full sm:w-56" />
                    <select wire:model.live="filterMahasiswa"
                        class="rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100 w-full sm:w-48">
                        <option value="">Semua Mahasiswa</option>
                        @foreach ($mahasiswas as $mhs)
                            <option value="{{ $mhs->id }}">{{ $mhs->user->name }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="filterStatusSesi"
                        class="rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100 w-full sm:w-44">
                        <option value="">Semua Status Sesi</option>
                        <option value="diajukan">Diajukan</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="selesai">Selesai</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                </div>
            </div>

            <div class="space-y-3">
                @forelse ($logs as $log)
                    @php
                        $isSelesai = ($log->status_sesi ?? 'diajukan') === 'selesai';
                    @endphp
                    <div wire:key="log-{{ $log->id }}"
                        class="rounded-2xl border border-slate-200 p-4 transition hover:border-slate-300 {{ $editId === $log->id ? 'border-indigo-300 bg-indigo-50/40' : '' }}">
                        @if ($isSelesai)
                            <details>
                                <summary class="cursor-pointer list-none">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-semibold text-slate-900">
                                                {{ $log->mahasiswa?->user?->name ?? '-' }}
                                            </p>
                                            <p class="text-xs text-slate-500">
                                                {{ $log->mahasiswa?->nim ?? '-' }} •
                                                {{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('d M Y') }}
                                                {{ $log->jam ? ' • ' . \Carbon\Carbon::parse($log->jam)->format('H:i') : '' }}
                                            </p>
                                        </div>
                                        <span
                                            class="shrink-0 rounded-full px-3 py-1 text-xs font-semibold bg-emerald-100 text-emerald-700">
                                            Sesi Selesai • Klik Detail
                                        </span>
                                    </div>
                                </summary>
                                <div class="mt-3 space-y-3 border-t border-slate-200 pt-3">
                                @else
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-semibold text-slate-900">
                                                {{ $log->mahasiswa?->user?->name ?? '-' }}
                                            </p>
                                            <p class="text-xs text-slate-500">
                                                {{ $log->mahasiswa?->nim ?? '-' }} •
                                                {{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('d M Y') }}
                                                {{ $log->jam ? ' • ' . \Carbon\Carbon::parse($log->jam)->format('H:i') : '' }}
                                            </p>
                                        </div>
                                        <span
                                            class="shrink-0 rounded-full px-3 py-1 text-xs font-semibold bg-blue-100 text-blue-700">
                                            Penjadwalan Aktif
                                        </span>
                                    </div>
                        @endif

                        <p class="mt-2 text-sm text-slate-600">
                            {{ $log->catatan ?: 'Belum ada agenda/catatan.' }}
                        </p>
                        <div class="mt-2 flex flex-wrap gap-2 text-xs">
                            <span
                                class="rounded-full px-2.5 py-1 {{ ($log->status_sesi ?? 'diajukan') === 'selesai' ? 'bg-emerald-100 text-emerald-700' : (($log->status_sesi ?? 'diajukan') === 'disetujui' ? 'bg-blue-100 text-blue-700' : (($log->status_sesi ?? 'diajukan') === 'dibatalkan' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700')) }}">
                                Status Sesi: {{ ucfirst($log->status_sesi ?? 'diajukan') }}
                            </span>
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-slate-700">
                                Mode: {{ ucfirst($log->mode ?? 'offline') }}
                            </span>
                            @if (($log->mode ?? 'offline') === 'online' && $log->link_online)
                                <a href="{{ $log->link_online }}" target="_blank"
                                    class="rounded-full bg-blue-50 px-2.5 py-1 font-semibold text-blue-700">Link
                                    Meeting</a>
                            @elseif (($log->mode ?? 'offline') === 'offline')
                                <span class="rounded-full bg-blue-50 px-2.5 py-1 text-blue-700">
                                    Lokasi: {{ $log->lokasi ?: 'Belum ditentukan' }}
                                </span>
                            @endif
                            <span
                                class="rounded-full px-2.5 py-1 {{ ($log->konfirmasi_mahasiswa ?? 'pending') === 'hadir' ? 'bg-emerald-100 text-emerald-700' : (($log->konfirmasi_mahasiswa ?? 'pending') === 'tidak_hadir' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                                Kehadiran Mahasiswa:
                                {{ ($log->konfirmasi_mahasiswa ?? 'pending') === 'hadir' ? 'Hadir' : (($log->konfirmasi_mahasiswa ?? 'pending') === 'tidak_hadir' ? 'Tidak Hadir' : 'Menunggu') }}
                            </span>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <button wire:click="edit({{ $log->id }})"
                                class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                                Edit
                            </button>
                            @if (($log->status_sesi ?? 'diajukan') !== 'selesai')
                                <button wire:click="ubahStatusSesi({{ $log->id }}, 'selesai')"
                                    class="rounded-lg border border-emerald-200 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-50">
                                    Tandai Selesai
                                </button>
                            @endif
                            @if (($log->status_sesi ?? 'diajukan') !== 'dibatalkan')
                                <button wire:click="ubahStatusSesi({{ $log->id }}, 'dibatalkan')"
                                    class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50">
                                    Batalkan Sesi
                                </button>
                            @endif
                            <button wire:click="hapus({{ $log->id }})"
                                wire:confirm="Hapus jadwal bimbingan ini?"
                                class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50">
                                Hapus
                            </button>
                        </div>

                        <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                            <p class="text-xs font-semibold text-slate-700">Audit Status Sesi</p>
                            <div class="mt-2 space-y-1.5">
                                @forelse ($log->sessionAudits->take(3) as $audit)
                                    <p class="text-[11px] text-slate-600">
                                        {{ $audit->changed_at?->translatedFormat('d M Y H:i') ?? '-' }} •
                                        {{ $audit->from_status_sesi ? ucfirst($audit->from_status_sesi) : '-' }}
                                        -> {{ ucfirst($audit->to_status_sesi) }}
                                        ({{ $audit->source }})
                                        oleh {{ $audit->changedByUser?->name ?? 'Sistem' }}
                                    </p>
                                @empty
                                    <p class="text-[11px] text-slate-500">Belum ada riwayat perubahan status sesi.</p>
                                @endforelse
                            </div>
                        </div>
                        @if ($isSelesai)
                    </div>
                    </details>
                @endif
            </div>
        @empty
            <div
                class="rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-sm text-slate-500">
                Belum ada penjadwalan bimbingan.
            </div>
            @endforelse
    </div>

    <div class="mt-4">
        <x-ui.show-entries wire:model.live="perPage" class="focus:border-indigo-500 focus:ring-indigo-500" />
    </div>

    <div class="mt-3">
        {{ $logs->links('vendor.pagination.tailwind') }}
    </div>
    </section>
</div>
</div>
