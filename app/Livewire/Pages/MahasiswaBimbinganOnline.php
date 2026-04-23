<?php

namespace App\Livewire\Pages;

use App\Models\BimbinganMessage;
use App\Models\Bimbingans;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class MahasiswaBimbinganOnline extends Component
{
    use WithFileUploads;
    use WithPagination;
    use WithoutUrlPagination;

    #[Title('Bimbingan Online')]
    public int|string $selected_dosen_id = '';
    public string $message = '';
    public $attachment;

    private function isChatEnabled(int $mahasiswaId, int $dosenId): bool
    {
        return \App\Models\BimbinganLog::query()
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('dosen_id', $dosenId)
            ->where('konfirmasi_mahasiswa', 'hadir')
            ->exists();
    }

    public function updatedSelectedDosenId(): void
    {
        $this->resetPage('chatPage');
    }

    public function kirim(): void
    {
        $this->validate([
            'selected_dosen_id' => ['required', 'integer', 'exists:dosens,id'],
            'message' => ['nullable', 'string', 'max:3000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,png,jpg,jpeg', 'max:5120'],
        ]);

        if (trim($this->message) === '' && ! $this->attachment) {
            $this->addError('message', 'Pesan atau lampiran wajib diisi.');
            return;
        }

        $mahasiswa = Auth::user()?->mahasiswa;
        if (! $mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan untuk akun ini.');
        }

        $dosenId = (int) $this->selected_dosen_id;

        $isOwned = Bimbingans::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('dosen_id', $dosenId)
            ->exists();

        if (! $isOwned) {
            $this->addError('selected_dosen_id', 'Dosen tidak valid untuk mahasiswa ini.');
            return;
        }

        if (! $this->isChatEnabled((int) $mahasiswa->id, $dosenId)) {
            $this->addError('message', 'Chat dikunci. Ikuti jadwal bimbingan dosen terlebih dahulu.');
            return;
        }

        $attachmentPath = null;
        if ($this->attachment) {
            $filename = now()->format('YmdHis')
                . '-' . Str::slug($mahasiswa->nim . '-chat')
                . '.' . $this->attachment->getClientOriginalExtension();

            $attachmentPath = $this->attachment->storeAs('bimbingan-chat/' . $mahasiswa->nim . '/dosen-' . $dosenId, $filename, 'public');
        }

        BimbinganMessage::query()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosenId,
            'sender_role' => 'mahasiswa',
            'message' => trim($this->message) ?: null,
            'attachment' => $attachmentPath,
        ]);

        $this->reset(['message', 'attachment']);
        $this->resetPage('chatPage');
        $this->dispatch('notify', message: 'Pesan bimbingan berhasil dikirim.');
    }

    public function render()
    {
        $mahasiswa = Auth::user()?->mahasiswa;
        if (! $mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan untuk akun ini.');
        }

        $dosens = Bimbingans::query()
            ->with('dosen.user')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('id')
            ->get()
            ->pluck('dosen')
            ->filter();

        if ($this->selected_dosen_id === '' && $dosens->isNotEmpty()) {
            $this->selected_dosen_id = (string) $dosens->first()->id;
        }

        if ($this->selected_dosen_id !== '') {
            BimbinganMessage::query()
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('dosen_id', (int) $this->selected_dosen_id)
                ->where('sender_role', 'dosen')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        $dosenList = $dosens->map(function ($dosen) use ($mahasiswa) {
            $chatEnabled = $this->isChatEnabled((int) $mahasiswa->id, (int) $dosen->id);

            $unreadCount = BimbinganMessage::query()
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('dosen_id', $dosen->id)
                ->where('sender_role', 'dosen')
                ->whereNull('read_at')
                ->count();

            $lastMessage = BimbinganMessage::query()
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('dosen_id', $dosen->id)
                ->latest('id')
                ->first();

            return [
                'id' => $dosen->id,
                'name' => $dosen->user?->name ?? 'Dosen',
                'unread_count' => $unreadCount,
                'last_preview' => $lastMessage?->message ?: ($lastMessage?->attachment ? 'Lampiran file' : 'Belum ada pesan'),
                'last_at' => $lastMessage?->created_at,
                'chat_enabled' => $chatEnabled,
            ];
        })->values();

        $chatEnabled = false;
        if ($this->selected_dosen_id !== '') {
            $chatEnabled = $this->isChatEnabled((int) $mahasiswa->id, (int) $this->selected_dosen_id);
        }

        $messages = BimbinganMessage::query()
            ->with('dosen.user')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->when($this->selected_dosen_id !== '', fn($q) => $q->where('dosen_id', (int) $this->selected_dosen_id))
            ->when(! $chatEnabled, fn($q) => $q->whereRaw('1=0'))
            ->oldest('id')
            ->paginate(12, ['*'], 'chatPage');

        return view('livewire.pages.mahasiswa-bimbingan-online', [
            'mahasiswa' => $mahasiswa,
            'dosenList' => $dosenList,
            'messages' => $messages,
            'chatEnabled' => $chatEnabled,
        ]);
    }
}
