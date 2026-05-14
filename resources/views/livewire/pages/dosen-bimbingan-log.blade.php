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

                    {{-- Jam --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Jam Bimbingan</label>
                        <input type="time" wire:model="jam"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100" />
                        @error('jam')
                            <x-ui.validation-error :message="$message" />
                        @enderror
                    </div>

                    {{-- Mode --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Mode Bimbingan</label>
                        <select wire:model="mode"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                            <option value="offline">Offline (Tatap Muka)</option>
                            <option value="online">Online (Sinkron)</option>
                        </select>
                        @error('mode')
                            <x-ui.validation-error :message="$message" />
                        @enderror
                    </div>

                    {{-- Lokasi / Link --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            {{ ($mode ?? 'offline') === 'online' ? 'Link Meeting' : 'Lokasi Bimbingan' }}
                        </label>
                        <input type="text" wire:model="lokasi"
                            placeholder="{{ ($mode ?? 'offline') === 'online' ? 'https://zoom.us/j/123...' : 'Ruang E2.11' }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100" />
                        @error('lokasi')
                            <x-ui.validation-error :message="$message" />
                        @enderror
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Agenda / Catatan</label>
                        <textarea wire:model="catatan" rows="3" placeholder="Topik yang akan dibahas pada sesi ini..."
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"></textarea>
                        @error('catatan')
                            <x-ui.validation-error :message="$message" />
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <button wire:click="simpan" wire:loading.attr="disabled"
                        class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 font-semibold text-white hover:bg-indigo-700 disabled:opacity-60">
                        <span wire:loading.remove>{{ $editId ? 'Perbarui Jadwal' : 'Buat Jadwal' }}</span>
                        <span wire:loading>Memproses...</span>
                    </button>

                    @if ($editId)
                        <button wire:click="batal"
                            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 font-semibold text-slate-600 hover:bg-slate-50">
                            Batal
                        </button>
                    @endif

                    <div class="border-t border-slate-200 pt-4">
                        <p class="mb-2 text-xs font-semibold text-slate-700 uppercase">Stats</p>
                        <div class="space-y-1">
                            @php
                                $countAll = $logs->count();
                                $countSelesai = $logs->where('status_sesi', 'selesai')->count();
                            @endphp
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500">Total Jadwal:</span>
                                <span class="font-semibold text-slate-900">{{ $countAll ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500">Selesai:</span>
                                <span class="font-semibold text-emerald-600">{{ $countSelesai ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- LOGS LISTING --}}
        <section class="space-y-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-base font-semibold text-slate-900">Jadwal Bimbingan</h2>

                <div class="space-y-3">
                    @php
                        $rendered = [];
                        $collection = $logs->getCollection();
                    @endphp

                    @forelse ($collection as $log)
                        @php
                            $sessionKey = md5(
                                ($log->tanggal ?? '') .
                                    '|' .
                                    ($log->jam ?? '') .
                                    '|' .
                                    ($log->mode ?? '') .
                                    '|' .
                                    ($log->lokasi ?? ''),
                            );
                        @endphp

                        @if (in_array($sessionKey, $rendered, true))
                            @continue
                        @endif

                        @php
                            $rendered[] = $sessionKey;
                            $sessionItems = $collection
                                ->where('tanggal', $log->tanggal)
                                ->where('jam', $log->jam)
                                ->where('mode', $log->mode)
                                ->where('lokasi', $log->lokasi);
                            $first = $sessionItems->first();
                            $isSelesai = ($first->status_sesi ?? 'diajukan') === 'selesai';
                        @endphp

                        <div class="rounded-2xl border border-slate-200 p-4 hover:border-slate-300 transition">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900">
                                        Sesi: {{ \Carbon\Carbon::parse($first->tanggal)->translatedFormat('d M Y') }}
                                        {{ $first->jam ? ' • ' . \Carbon\Carbon::parse($first->jam)->format('H:i') : '' }}
                                        - {{ ucfirst($first->mode ?? 'offline') }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        Lokasi: {{ $first->lokasi ?: '—' }} • Peserta: {{ $sessionItems->count() }}
                                        mahasiswa
                                    </p>
                                </div>
                                <span
                                    class="shrink-0 rounded-full px-3 py-1 text-xs font-semibold {{ $isSelesai ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $isSelesai ? 'Selesai' : 'Aktif' }}
                                </span>
                            </div>

                            <p class="mt-2 text-sm text-slate-600">
                                {{ $first->catatan ?: 'Belum ada agenda/catatan.' }}
                            </p>

                            <div class="mt-3 flex gap-2 flex-wrap">
                                <button
                                    wire:click="editSession('{{ $first->tanggal }}','{{ $first->jam }}','{{ $first->mode }}','{{ $first->lokasi }}', {{ $first->id }})"
                                    class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                                    Edit Sesi
                                </button>
                                @if (($first->status_sesi ?? 'diajukan') !== 'selesai')
                                    <button
                                        wire:click="ubahStatusSesiSession('{{ $first->tanggal }}','{{ $first->jam }}','{{ $first->mode }}','{{ $first->lokasi }}','selesai')"
                                        class="rounded-lg border border-emerald-200 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-50">
                                        Tandai Selesai
                                    </button>
                                @endif
                                @if (($first->status_sesi ?? 'diajukan') !== 'dibatalkan')
                                    <button
                                        wire:click="ubahStatusSesiSession('{{ $first->tanggal }}','{{ $first->jam }}','{{ $first->mode }}','{{ $first->lokasi }}','dibatalkan')"
                                        class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50">
                                        Batalkan Sesi
                                    </button>
                                @endif
                                <button
                                    wire:click="hapusSession('{{ $first->tanggal }}','{{ $first->jam }}','{{ $first->mode }}','{{ $first->lokasi }}')"
                                    wire:confirm="Hapus semua jadwal pada sesi ini?"
                                    class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50">
                                    Hapus Sesi
                                </button>
                            </div>

                            {{-- List Mahasiswa dalam Session --}}
                            <div class="mt-3 space-y-2">
                                @foreach ($sessionItems as $slog)
                                    <div class="rounded-lg border border-slate-100 px-3 py-2">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-semibold text-slate-800">
                                                    {{ $slog->mahasiswa?->user?->name ?? 'Mahasiswa' }}
                                                </p>
                                                <p class="text-xs text-slate-500">
                                                    {{ $slog->mahasiswa?->nim ?? '-' }} • Kehadiran:
                                                    {{ $slog->konfirmasi_mahasiswa ?? 'pending' }}
                                                </p>
                                            </div>
                                            <div class="text-xs text-slate-500">
                                                {{ $slog->created_at?->translatedFormat('d M Y H:i') }}
                                            </div>
                                        </div>

                                        @if (!empty($slog->catatan_mahasiswa))
                                            <div class="mt-2 rounded-lg border border-sky-200 bg-sky-50 px-3 py-2">
                                                <p
                                                    class="text-[11px] font-semibold uppercase tracking-wide text-sky-800">
                                                    Catatan Hasil Bimbingan dari Mahasiswa</p>
                                                <p class="mt-1 whitespace-pre-line text-xs text-sky-900">
                                                    {{ $slog->catatan_mahasiswa }}</p>
                                            </div>
                                        @endif

                                        {{-- Catatan Revisi per Mahasiswa --}}
                                        <div class="mt-2 border-t border-slate-100 pt-2">
                                            <textarea wire:model="catatanRevisi.{{ $slog->id }}" rows="2"
                                                placeholder="Tulis catatan revisi untuk mahasiswa ini..."
                                                class="w-full rounded-lg border border-amber-200 bg-white px-2 py-1.5 text-xs text-slate-900 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-100"></textarea>
                                            <div class="mt-1 flex gap-2">
                                                <button wire:click="kirimCatatanRevisi({{ $slog->id }})"
                                                    wire:loading.attr="disabled"
                                                    class="rounded-lg bg-amber-600 px-2 py-1 text-xs font-semibold text-white hover:bg-amber-700 disabled:opacity-60">
                                                    <span wire:loading.remove
                                                        wire:target="kirimCatatanRevisi({{ $slog->id }})">Kirim
                                                        Catatan</span>
                                                    <span wire:loading
                                                        wire:target="kirimCatatanRevisi({{ $slog->id }})">Mengirim...</span>
                                                </button>
                                            </div>

                                            {{-- Display Sent Messages --}}
                                            @if ($slog->bimbinganMessages && $slog->bimbinganMessages->where('sender_role', 'dosen')->isNotEmpty())
                                                <div class="mt-1.5 space-y-1">
                                                    @foreach ($slog->bimbinganMessages->where('sender_role', 'dosen')->take(2) as $message)
                                                        <div
                                                            class="rounded-lg border border-amber-200 bg-amber-50 px-2 py-1.5 text-xs text-slate-700">
                                                            <p class="font-semibold text-amber-800">✓
                                                                {{ $message->created_at?->translatedFormat('d M H:i') }}
                                                            </p>
                                                            <p class="mt-0.5 whitespace-pre-line text-[11px]">
                                                                {{ $message->message }}</p>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Audit Section --}}
                            <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                                <p class="text-xs font-semibold text-slate-700">Audit Status Sesi</p>
                                <div class="mt-2 space-y-1.5">
                                    @forelse ($first->sessionAudits->take(3) as $audit)
                                        <p class="text-[11px] text-slate-600">
                                            {{ $audit->changed_at?->translatedFormat('d M Y H:i') ?? '-' }} •
                                            {{ $audit->from_status_sesi ? ucfirst($audit->from_status_sesi) : '-' }}
                                            -> {{ ucfirst($audit->to_status_sesi) }} ({{ $audit->source }})
                                            oleh {{ $audit->changedByUser?->name ?? 'Sistem' }}
                                        </p>
                                    @empty
                                        <p class="text-[11px] text-slate-500">Belum ada riwayat perubahan status sesi.
                                        </p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-sm text-slate-500">
                            Belum ada penjadwalan bimbingan.
                        </div>
                    @endforelse
                </div>

                <div class="mt-4">
                    <x-ui.show-entries wire:model.live="perPage"
                        class="focus:border-indigo-500 focus:ring-indigo-500" />
                </div>

                <div class="mt-3">
                    {{ $logs->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </section>
    </div>
</div>
