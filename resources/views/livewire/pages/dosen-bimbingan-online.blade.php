<div class="space-y-8">
    <section
        class="overflow-hidden rounded-3xl bg-linear-to-r from-slate-900 via-indigo-900 to-blue-800 px-6 py-8 text-white shadow-lg sm:px-8">
        <div class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-indigo-100/80">Portal Dosen</p>
            <h1 class="text-3xl font-semibold sm:text-4xl">Chat Bimbingan Online</h1>
            <p class="max-w-2xl text-sm text-indigo-100 sm:text-base">Balas pesan konsultasi mahasiswa dan kirim file
                pendukung bimbingan.</p>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-[340px_1fr]">
        <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900">Daftar Mahasiswa</h2>
            <p class="mt-1 text-xs text-slate-500">Tanda dot merah = ada pesan baru dari mahasiswa.</p>

            <div class="mt-4 space-y-2">
                @forelse ($mahasiswaList as $item)
                    <button type="button" wire:click="$set('selected_mahasiswa_id', '{{ $item['id'] }}')"
                        class="w-full rounded-2xl border px-3 py-3 text-left transition {{ (string) $selected_mahasiswa_id === (string) $item['id'] ? 'border-indigo-300 bg-indigo-50' : 'border-slate-200 hover:border-slate-300' }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-900">{{ $item['name'] }}</p>
                                <p class="text-xs text-slate-500">{{ $item['nim'] }}</p>
                                <p class="mt-1 truncate text-xs text-slate-500">{{ $item['last_preview'] }}</p>
                                <p class="mt-1 text-[11px] text-slate-400">
                                    {{ $item['last_at']?->translatedFormat('d M H:i') ?? '-' }}</p>
                                @if (!$item['chat_enabled'])
                                    <p class="mt-1 text-[11px] font-semibold text-amber-600">Chat terkunci</p>
                                @endif
                            </div>
                            @if ($item['unread_count'] > 0)
                                <span class="mt-1 inline-flex size-2.5 rounded-full bg-rose-500"></span>
                            @endif
                        </div>
                    </button>
                @empty
                    <div
                        class="rounded-2xl border border-dashed border-slate-200 px-3 py-6 text-center text-sm text-slate-500">
                        Belum ada mahasiswa bimbingan.
                    </div>
                @endforelse
            </div>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="border-b border-slate-200 pb-4">
                <h2 class="text-lg font-semibold text-slate-900">Thread Chat</h2>
                @if (!$chatEnabled)
                    <p class="mt-1 text-xs font-medium text-amber-600">
                        Chat nonaktif sampai mahasiswa mengikuti minimal 1 jadwal bimbingan.
                    </p>
                @endif
            </div>

            <div class="mt-4 space-y-3 max-h-96 overflow-y-auto pr-1">
                @forelse ($messages as $msg)
                    @php $isMine = $msg->sender_role === 'dosen'; @endphp
                    <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                        <div
                            class="max-w-[80%] rounded-2xl px-4 py-3 {{ $isMine ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700' }}">
                            <p class="text-[11px] {{ $isMine ? 'text-indigo-100' : 'text-slate-500' }}">
                                {{ $isMine ? 'Anda' : $msg->mahasiswa?->user?->name ?? 'Mahasiswa' }} •
                                {{ $msg->created_at?->translatedFormat('d M Y H:i') }}
                            </p>
                            @if ($msg->message)
                                <p class="mt-1 text-sm">{{ $msg->message }}</p>
                            @endif
                            @if ($msg->attachment)
                                <a href="{{ Storage::url($msg->attachment) }}" target="_blank"
                                    class="mt-2 inline-flex rounded-lg px-2 py-1 text-xs font-semibold {{ $isMine ? 'bg-indigo-500 text-white' : 'bg-white text-indigo-700' }}">
                                    Lihat lampiran
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div
                        class="rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-sm text-slate-500">
                        Belum ada pesan pada thread ini.
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $messages->links('vendor.pagination.tailwind') }}
            </div>

            <form wire:submit.prevent="kirim" novalidate class="mt-5 space-y-3 border-t border-slate-200 pt-4">
                <input type="hidden" wire:model="selected_mahasiswa_id" />

                <div>
                    <textarea wire:model="message" rows="3" placeholder="Tulis balasan..."
                        class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"></textarea>
                    @error('selected_mahasiswa_id')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                    @error('message')
                        <x-ui.validation-error :message="$message" />
                    @enderror
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <input type="file" wire:model="attachment"
                        class="block w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100 sm:max-w-xs" />
                    <button type="submit" @disabled(!$chatEnabled)
                        class="rounded-2xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500">
                        Kirim Balasan
                    </button>
                </div>
                @error('attachment')
                    <x-ui.validation-error :message="$message" />
                @enderror
            </form>
        </article>
    </section>
</div>
