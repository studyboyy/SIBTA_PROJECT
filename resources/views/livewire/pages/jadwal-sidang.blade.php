<div class="space-y-6">
    <div class="rounded-3xl bg-linear-to-r from-slate-900 via-blue-900 to-cyan-800 px-6 py-8 text-white shadow-lg">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.3em] text-cyan-100/80">Panel Admin</p>
                <h1 class="mt-2 text-2xl font-bold sm:text-3xl">Jadwal Sidang & Penentuan Penguji</h1>
                <p class="mt-2 max-w-3xl text-sm text-blue-100 sm:text-base">
                    Buat jadwal sidang lalu approve pengajuan mahasiswa agar otomatis masuk ke jadwal yang tersedia.
                </p>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- TAB DATA SIDANG --}}
    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="flex overflow-x-auto border-b border-slate-200">
            <button type="button" wire:click="setActiveTab('jadwal')"
                class="flex min-w-40 flex-1 items-center justify-center gap-2 px-4 py-3 text-sm font-semibold transition-colors {{ $activeTab === 'jadwal' ? 'border-b-2 border-blue-600 bg-blue-50/50 text-blue-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                <span>Jadwal & Batch</span>
            </button>
            <button type="button" wire:click="setActiveTab('approval')"
                class="flex min-w-44 flex-1 items-center justify-center gap-2 px-4 py-3 text-sm font-semibold transition-colors {{ $activeTab === 'approval' ? 'border-b-2 border-blue-600 bg-blue-50/50 text-blue-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                <span>Approval Pengajuan</span>
                <span class="rounded-full bg-white px-2 py-0.5 text-xs text-slate-500 shadow-sm">
                    {{ $pengajuanSidangs->total() }}
                </span>
            </button>
            <button type="button" wire:click="setActiveTab('sidang')"
                class="flex min-w-40 flex-1 items-center justify-center gap-2 px-4 py-3 text-sm font-semibold transition-colors {{ $activeTab === 'sidang' ? 'border-b-2 border-blue-600 bg-blue-50/50 text-blue-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                <span>Daftar Mahasiswa</span>
                <span class="rounded-full bg-white px-2 py-0.5 text-xs text-slate-500 shadow-sm">
                    {{ $mahasiswaBimbinganList->total() }}
                </span>
            </button>
        </div>

        @if ($activeTab === 'jadwal')
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">
                            {{ $editId ? 'Edit Jadwal Sidang' : 'Buat Jadwal Sidang' }}</h2>
                        <p class="mt-1 text-sm text-slate-500">Atur tanggal, ruangan, kuota, dan dosen penguji.</p>
                    </div>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                        Saran gel. {{ $nextWave }}
                    </span>
                </div>

                <form wire:submit.prevent="simpan" class="mt-5 space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Gelombang</label>
                        <input type="number" min="1" wire:model="gelombang"
                            placeholder="Kosongkan untuk otomatis"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                        @error('gelombang')
                            <x-ui.validation-error :message="$message" />
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Kuota Mahasiswa</label>
                        <input type="number" min="1" wire:model="kuotaPerGelombang"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                        @error('kuotaPerGelombang')
                            <x-ui.validation-error :message="$message" />
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Tanggal</label>
                        <input type="date" wire:model="tanggal"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                        @error('tanggal')
                            <x-ui.validation-error :message="$message" />
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Jam Mulai</label>
                            <input type="time" wire:model="jam_mulai"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                            @error('jam_mulai')
                                <x-ui.validation-error :message="$message" />
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Jam Selesai</label>
                            <input type="time" wire:model="jam_selesai"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                            @error('jam_selesai')
                                <x-ui.validation-error :message="$message" />
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Ruangan</label>
                        <input type="text" wire:model="ruangan" placeholder="Contoh: Ruang Sidang 1"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                        @error('ruangan')
                            <x-ui.validation-error :message="$message" />
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Ketua Sidang</label>
                        <select wire:model="ketua_sidang_id"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <option value="">-- Pilih Ketua Sidang --</option>
                            @foreach ($dosens as $dsn)
                                <option value="{{ $dsn->id }}">{{ $dsn->user->name }}</option>
                            @endforeach
                        </select>
                        @error('ketua_sidang_id')
                            <x-ui.validation-error :message="$message" />
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Penguji 1</label>
                        <select wire:model="penguji_1_id"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <option value="">-- Pilih Penguji 1 --</option>
                            @foreach ($dosens as $dsn)
                                <option value="{{ $dsn->id }}">{{ $dsn->user->name }}</option>
                            @endforeach
                        </select>
                        @error('penguji_1_id')
                            <x-ui.validation-error :message="$message" />
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Penguji 2</label>
                        <select wire:model="penguji_2_id"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <option value="">-- Pilih Penguji 2 --</option>
                            @foreach ($dosens as $dsn)
                                <option value="{{ $dsn->id }}">{{ $dsn->user->name }}</option>
                            @endforeach
                        </select>
                        @error('penguji_2_id')
                            <x-ui.validation-error :message="$message" />
                        @enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled"
                        class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <span wire:loading.remove
                            wire:target="simpan">{{ $editId ? 'Update Jadwal' : 'Simpan Jadwal' }}</span>
                        <span wire:loading wire:target="simpan">Menyimpan...</span>
                    </button>

                    @if ($editId)
                        <button type="button" wire:click="batalEdit"
                            class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Batal Edit
                        </button>
                    @endif
                </form>
            </div>
        </div>

        {{-- DAFTAR BATCH JADWAL --}}
        <div class="lg:col-span-2">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-slate-900">Daftar Batch Jadwal</h2>
                <p class="mt-1 text-sm text-slate-500">Setiap batch menampung mahasiswa sesuai kuota. Mahasiswa yang
                    diapprove otomatis masuk ke batch dengan kuota tersedia.</p>

                @if ($batches->isEmpty())
                    <p class="py-10 text-center text-sm text-slate-500">Belum ada batch jadwal.</p>
                @else
                    <div class="mt-4 space-y-3">
                        @foreach ($batches as $batch)
                            <div class="rounded-2xl border border-slate-200 p-4">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="font-semibold text-slate-900">Gelombang {{ $batch->gelombang }}
                                            &mdash; {{ $batch->ruangan }}</p>
                                        <p class="text-sm text-slate-500">
                                            {{ \Carbon\Carbon::parse($batch->tanggal)->translatedFormat('d M Y') }}
                                            &bull;
                                            {{ substr($batch->jam_mulai, 0, 5) }}&ndash;{{ substr($batch->jam_selesai, 0, 5) }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold text-cyan-700">
                                            Semua prodi
                                        </span>
                                        <span
                                            class="rounded-full {{ $batch->sidangs_count >= $batch->kuota ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }} px-3 py-1 text-xs font-semibold">
                                            {{ $batch->sidangs_count }}/{{ $batch->kuota }} terisi
                                        </span>
                                        <button wire:click="edit({{ $batch->id }})"
                                            class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-100">Edit</button>
                                        <button wire:click="hapusBatch({{ $batch->id }})"
                                            wire:confirm="Hapus batch gelombang {{ $batch->gelombang }}?"
                                            class="rounded-xl border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">Hapus</button>
                                    </div>
                                </div>
                                <div class="mt-2 flex flex-wrap gap-2 text-xs text-slate-600">
                                    <span class="rounded-full bg-blue-50 px-2.5 py-1">Ketua:
                                        {{ $batch->ketuaSidang?->user?->name ?? '-' }}</span>
                                    <span class="rounded-full bg-purple-50 px-2.5 py-1">Penguji 1:
                                        {{ $batch->penguji1?->user?->name ?? '-' }}</span>
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-1">Penguji 2:
                                        {{ $batch->penguji2?->user?->name ?? '-' }}</span>
                                </div>
                                @if ($batch->sidangs->isNotEmpty())
                                    <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-100">
                                        <table class="min-w-full text-xs">
                                            <thead class="bg-slate-50 text-left font-semibold uppercase tracking-wide text-slate-500">
                                                <tr>
                                                    <th class="px-3 py-2">Mahasiswa</th>
                                                    <th class="px-3 py-2">Prodi</th>
                                                    <th class="px-3 py-2">Status Sidang</th>
                                                    <th class="px-3 py-2 text-right">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                                @foreach ($batch->sidangs as $sidang)
                                                    @php
                                                        $sidangStatus = $sidang->status ?? 'pending';
                                                        $sidangStatusClass = match ($sidangStatus) {
                                                            'selesai', 'lulus' => 'bg-emerald-100 text-emerald-700',
                                                            'tidak_lulus' => 'bg-rose-100 text-rose-700',
                                                            default => 'bg-amber-100 text-amber-700',
                                                        };
                                                        $sidangStatusLabel = match ($sidangStatus) {
                                                            'tidak_lulus' => 'Tidak Lulus',
                                                            default => ucfirst(str_replace('_', ' ', $sidangStatus)),
                                                        };
                                                    @endphp
                                                    <tr>
                                                        <td class="px-3 py-2 font-medium text-slate-800">
                                                            {{ $sidang->mahasiswa?->user?->name ?? '-' }}
                                                            <span class="block font-normal text-slate-500">{{ $sidang->mahasiswa?->nim ?? '-' }}</span>
                                                        </td>
                                                        <td class="px-3 py-2 text-slate-600">
                                                            {{ $sidang->mahasiswa?->programStudi?->name ?? ($sidang->mahasiswa?->prodi ?? '-') }}
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            <span class="rounded-full px-2.5 py-1 font-semibold {{ $sidangStatusClass }}">
                                                                {{ $sidangStatusLabel }}
                                                            </span>
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            <div class="flex flex-wrap justify-end gap-1.5">
                                                                @if ($sidangStatus !== 'selesai')
                                                                    <button type="button" wire:click="updateSidangStatus({{ $sidang->id }}, 'selesai')"
                                                                        class="rounded-lg border border-emerald-200 px-2.5 py-1 font-semibold text-emerald-700 hover:bg-emerald-50">
                                                                        Selesai
                                                                    </button>
                                                                @endif
                                                                @if ($sidangStatus !== 'lulus')
                                                                    <button type="button" wire:click="updateSidangStatus({{ $sidang->id }}, 'lulus')"
                                                                        class="rounded-lg border border-blue-200 px-2.5 py-1 font-semibold text-blue-700 hover:bg-blue-50">
                                                                        Lulus
                                                                    </button>
                                                                @endif
                                                                @if ($sidangStatus !== 'tidak_lulus')
                                                                    <button type="button" wire:click="updateSidangStatus({{ $sidang->id }}, 'tidak_lulus')"
                                                                        class="rounded-lg border border-rose-200 px-2.5 py-1 font-semibold text-rose-700 hover:bg-rose-50">
                                                                        Tidak Lulus
                                                                    </button>
                                                                @endif
                                                                <button type="button" wire:click="hapusSidang({{ $sidang->id }})"
                                                                    wire:confirm="Hapus jadwal sidang mahasiswa ini?"
                                                                    class="rounded-lg border border-slate-200 px-2.5 py-1 font-semibold text-slate-600 hover:bg-slate-50">
                                                                    Hapus
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
                </div>
            </div>
        @elseif ($activeTab === 'approval')
            <div class="p-4 sm:p-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Approval Pengajuan Sidang</h2>
                        <p class="mt-1 text-sm text-slate-500">Approve pengajuan dan mahasiswa otomatis masuk ke
                            jadwal batch yang tersedia.</p>
                    </div>
                    <div class="w-full md:w-56">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Filter
                            Status</label>
                        <select wire:model.live="pengajuanStatus"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <option value="">Semua</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <div class="mt-5 space-y-4">
                    @forelse ($pengajuanSidangs as $pengajuan)
                        @php
                            $isApproved = ($pengajuan->status ?? 'pending') === 'approved';
                            $isRejected = ($pengajuan->status ?? 'pending') === 'rejected';
                        @endphp
                        <article class="rounded-2xl border border-slate-200 p-4">
                            <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                                <div>
                                    <p class="font-semibold text-slate-900">
                                        {{ $pengajuan->mahasiswa?->user?->name ?? 'Mahasiswa' }}</p>
                                    <p class="text-sm text-slate-500">{{ $pengajuan->mahasiswa?->nim ?? '-' }} &bull;
                                        diajukan {{ $pengajuan->diajukan_pada?->translatedFormat('d M Y H:i') ?? '-' }}
                                    </p>
                                    <p class="text-xs text-slate-400">Prodi:
                                        {{ $pengajuan->mahasiswa?->programStudi?->name ?? ($pengajuan->mahasiswa?->prodi ?? '-') }}
                                    </p>
                                </div>
                                <div class="flex flex-wrap gap-2 text-xs font-semibold">
                                    <span
                                        class="rounded-full px-3 py-1 {{ ($pengajuan->status_dosen ?? 'pending') === 'approved' ? 'bg-emerald-100 text-emerald-700' : (($pengajuan->status_dosen ?? 'pending') === 'rejected' ? 'bg-red-100 text-red-700' : (($pengajuan->status_dosen ?? 'pending') === 'revisi' ? 'bg-orange-100 text-orange-700' : 'bg-amber-100 text-amber-700')) }}">
                                        Dosen: {{ ucfirst($pengajuan->status_dosen ?? 'pending') }}
                                    </span>
                                    <span
                                        class="rounded-full px-3 py-1 {{ ($pengajuan->status_kaprodi ?? 'pending') === 'approved' ? 'bg-cyan-100 text-cyan-700' : (($pengajuan->status_kaprodi ?? 'pending') === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700') }}">
                                        Kaprodi: {{ ucfirst($pengajuan->status_kaprodi ?? 'pending') }}
                                    </span>
                                    <span
                                        class="rounded-full px-3 py-1 {{ $isApproved ? 'bg-emerald-100 text-emerald-700' : ($isRejected ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700') }}">
                                        Admin: {{ ucfirst($pengajuan->status ?? 'pending') }}
                                    </span>
                                    @if ($pengajuan->gelombang)
                                        <span class="rounded-full bg-blue-100 px-3 py-1 text-blue-700">
                                            Gelombang {{ $pengajuan->gelombang }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if ($pengajuan->catatan_mahasiswa)
                                <div class="mt-3 rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-700">
                                    <span class="font-semibold text-slate-900">Catatan mahasiswa:</span>
                                    {{ $pengajuan->catatan_mahasiswa }}
                                </div>
                            @endif

                            @if ($pengajuan->catatan_kaprodi)
                                <div class="mt-3 rounded-xl bg-cyan-50 px-4 py-3 text-sm text-cyan-800">
                                    <span class="font-semibold text-cyan-900">Catatan kaprodi:</span>
                                    {{ $pengajuan->catatan_kaprodi }}
                                </div>
                            @endif

                            @if ($isApproved)
                                <div class="mt-3 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                                    Pengajuan sudah disetujui dan dijadwalkan ke gelombang
                                    {{ $pengajuan->gelombang ?? '-' }}.
                                </div>
                            @elseif ($isRejected)
                                <div class="mt-3 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-800">
                                    Pengajuan telah ditolak.
                                </div>
                            @elseif (($pengajuan->status_kaprodi ?? 'pending') !== 'approved')
                                <div class="mt-3 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-800">
                                    Menunggu approval kaprodi. Admin belum bisa menjadwalkan sidang sebelum tahap ini
                                    disetujui.
                                </div>
                            @else
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <button wire:click="approvePengajuan({{ $pengajuan->id }})"
                                        wire:confirm="Setujui pengajuan ini? Mahasiswa akan otomatis dijadwalkan ke batch tersedia."
                                        class="rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                                        Approve & Jadwalkan
                                    </button>
                                    <button wire:click="rejectPengajuan({{ $pengajuan->id }})"
                                        wire:confirm="Tolak pengajuan ini?"
                                        class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-700">
                                        Reject
                                    </button>
                                </div>
                            @endif
                        </article>
                    @empty
                        <div
                            class="rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-sm text-slate-500">
                            Belum ada pengajuan sidang mahasiswa.
                        </div>
                    @endforelse
                </div>

                <div class="mt-4">
                    <x-ui.show-entries wire:model.live="pengajuanPerPage"
                        class="focus:border-blue-500 focus:ring-blue-500" />
                </div>

                <div class="mt-3">
                    {{ $pengajuanSidangs->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        @elseif ($activeTab === 'sidang')
            <div class="p-4 sm:p-6">
                <div class="mb-4 flex flex-col gap-3 xl:flex-row xl:items-end xl:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Daftar Mahasiswa Bimbingan</h2>
                        <p class="mt-1 text-sm text-slate-500">Mahasiswa yang sudah memiliki dosen pembimbing.</p>
                    </div>
                    <div class="grid w-full grid-cols-1 gap-2 sm:grid-cols-2 xl:w-auto xl:grid-cols-[240px_180px_220px]">
                        <input type="text" wire:model.live.debounce.400ms="mahasiswaSearch"
                            placeholder="Cari nama, NIM, pembimbing..."
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                        <select wire:model.live="mahasiswaStatus"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <option value="">Semua Status</option>
                            <option value="layak">Layak Sidang</option>
                            <option value="belum_layak">Belum Layak</option>
                        </select>
                        <select wire:model.live="mahasiswaProdi"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 sm:col-span-2 xl:col-span-1">
                            <option value="">Semua Prodi</option>
                            @foreach ($prodiOptions as $prodi)
                                <option value="{{ $prodi->id }}">{{ $prodi->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if ($mahasiswaBimbinganList->isEmpty())
                    <p class="py-10 text-center text-sm text-slate-500">Belum ada mahasiswa dengan dosen pembimbing.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse text-sm">
                            <thead>
                                <tr
                                    class="bg-slate-100 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                    <th class="px-4 py-3">Nama</th>
                                    <th class="px-4 py-3">NIM</th>
                                    <th class="px-4 py-3">Program Studi</th>
                                    <th class="px-4 py-3">Pembimbing</th>
                                    <th class="px-4 py-3">Kaprodi</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($mahasiswaBimbinganList as $bimbingan)
                                    @php
                                        $mahasiswa = $bimbingan->mahasiswa;
                                        $pengajuanSidang = $mahasiswa?->pengajuanSidang;
                                        $isLayak = ($pengajuanSidang?->status_dosen ?? 'pending') === 'approved';
                                    @endphp
                                    <tr wire:key="mahasiswa-bimbingan-sidang-{{ $bimbingan->id }}"
                                        class="hover:bg-slate-50">
                                        <td class="px-4 py-3 font-medium text-slate-800">
                                            {{ $mahasiswa?->user?->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-slate-600">
                                            {{ $mahasiswa?->nim ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-slate-600">
                                            {{ $mahasiswa?->programStudi?->name ?? ($mahasiswa?->prodi ?? '-') }}
                                        </td>
                                        <td class="px-4 py-3 text-slate-700">
                                            {{ $bimbingan->dosen?->user?->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-slate-600">
                                            {{ $mahasiswa?->programStudi?->kaprodiUser?->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $isLayak ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                                {{ $isLayak ? 'Layak Sidang' : 'Belum Layak' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <x-ui.show-entries wire:model.live="mahasiswaPerPage"
                            class="focus:border-blue-500 focus:ring-blue-500" />
                    </div>

                    <div class="mt-3">
                        {{ $mahasiswaBimbinganList->links('vendor.pagination.tailwind') }}
                    </div>
                @endif
            </div>
        @endif
    </section>
</div>
