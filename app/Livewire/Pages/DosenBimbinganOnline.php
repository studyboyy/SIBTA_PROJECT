<?php

namespace App\Livewire\Pages;

use App\Models\BimbinganMessage;
use App\Models\Bimbingans;
use App\Models\Mahasiswas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class DosenBimbinganOnline extends Component
{
    use WithFileUploads;
    use WithPagination;
    use WithoutUrlPagination;

    #[Title('Chat Bimbingan Online')]
    public int|string $selected_mahasiswa_id = '';
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

    public function updatedSelectedMahasiswaId(): void
    {
        $this->resetPage('chatPage');
    }

    private function getDosen()
    {
        $dosen = Auth::user()?->dosen;

        if (! $dosen) {
            abort(403, 'Akun ini tidak terhubung ke data dosen.');
        }

        return $dosen;
    }

    public function kirim(): void
    {
        $this->validate([
            'selected_mahasiswa_id' => ['required', 'integer', 'exists:mahasiswas,id'],
            'message' => ['nullable', 'string', 'max:3000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,png,jpg,jpeg', 'max:5120'],
        ]);

        if (trim($this->message) === '' && ! $this->attachment) {
            $this->addError('message', 'Pesan atau lampiran wajib diisi.');
            return;
        }

        $dosen = $this->getDosen();

        $selectedMahasiswaId = (int) $this->selected_mahasiswa_id;

        $isOwned = Bimbingans::query()
            ->where('dosen_id', $dosen->id)
            ->where('mahasiswa_id', $selectedMahasiswaId)
            ->exists();

        if (! $isOwned) {
            $this->addError('selected_mahasiswa_id', 'Mahasiswa ini bukan bimbingan Anda.');
            return;
        }

        if (! $this->isChatEnabled($selectedMahasiswaId, (int) $dosen->id)) {
            $this->addError('message', 'Chat dikunci sampai mahasiswa mengikuti jadwal bimbingan.');
            return;
        }

        $attachmentPath = null;

        if ($this->attachment) {
            $filename = now()->format('YmdHis')
                . '-' . Str::slug('dosen-' . $dosen->id . '-chat')
                . '.' . $this->attachment->getClientOriginalExtension();

            $attachmentPath = $this->attachment->storeAs('bimbingan-chat/dosen-' . $dosen->id, $filename, 'public');
        }

        BimbinganMessage::query()->create([
            'mahasiswa_id' => $selectedMahasiswaId,
            'dosen_id' => $dosen->id,
            'sender_role' => 'dosen',
            'message' => trim($this->message) ?: null,
            'attachment' => $attachmentPath,
        ]);

        $this->reset(['message', 'attachment']);
        $this->resetPage('chatPage');
        $this->dispatch('notify', message: 'Balasan berhasil dikirim.');
    }

    public function render()
    {
        $dosen = $this->getDosen();

        $mahasiswaIds = Bimbingans::query()
            ->where('dosen_id', $dosen->id)
            ->pluck('mahasiswa_id');

        $mahasiswas = Mahasiswas::query()
            ->with('user')
            ->whereIn('id', $mahasiswaIds)
            ->orderBy('nim')
            ->get();

        if ($this->selected_mahasiswa_id === '' && $mahasiswas->isNotEmpty()) {
            $this->selected_mahasiswa_id = (string) $mahasiswas->first()->id;
        }

        if ($this->selected_mahasiswa_id !== '') {
            BimbinganMessage::query()
                ->where('dosen_id', $dosen->id)
                ->where('mahasiswa_id', (int) $this->selected_mahasiswa_id)
                ->where('sender_role', 'mahasiswa')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        $mahasiswaList = $mahasiswas->map(function ($mhs) use ($dosen) {
            $chatEnabled = $this->isChatEnabled((int) $mhs->id, (int) $dosen->id);

            $unreadCount = BimbinganMessage::query()
                ->where('dosen_id', $dosen->id)
                ->where('mahasiswa_id', $mhs->id)
                ->where('sender_role', 'mahasiswa')
                ->whereNull('read_at')
                ->count();

            $lastMessage = BimbinganMessage::query()
                ->where('dosen_id', $dosen->id)
                ->where('mahasiswa_id', $mhs->id)
                ->latest('id')
                ->first();

            return [
                'id' => $mhs->id,
                'name' => $mhs->user?->name ?? 'Mahasiswa',
                'nim' => $mhs->nim,
                'unread_count' => $unreadCount,
                'last_preview' => $lastMessage?->message ?: ($lastMessage?->attachment ? 'Lampiran file' : 'Belum ada pesan'),
                'last_at' => $lastMessage?->created_at,
                'chat_enabled' => $chatEnabled,
            ];
        })->values();

        $chatEnabled = false;
        if ($this->selected_mahasiswa_id !== '') {
            $chatEnabled = $this->isChatEnabled((int) $this->selected_mahasiswa_id, (int) $dosen->id);
        }

        $messages = BimbinganMessage::query()
            ->with(['mahasiswa.user'])
            ->where('dosen_id', $dosen->id)
            ->when($this->selected_mahasiswa_id !== '', fn($q) => $q->where('mahasiswa_id', (int) $this->selected_mahasiswa_id))
            ->when(! $chatEnabled, fn($q) => $q->whereRaw('1=0'))
            ->oldest('id')
            ->paginate(12, ['*'], 'chatPage');

        return view('livewire.pages.dosen-bimbingan-online', [
            'dosen' => $dosen,
            'mahasiswaList' => $mahasiswaList,
            'messages' => $messages,
            'chatEnabled' => $chatEnabled,
        ]);
    }
}
