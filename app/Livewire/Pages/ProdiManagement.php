<?php

namespace App\Livewire\Pages;

use App\Models\Prodi;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class ProdiManagement extends Component
{
    use WithPagination;

    public string $name = '';
    public string $code = '';
    public string $kaprodi_user_id = '';
    public ?int $editId = null;
    public ?int $deleteId = null;
    public string $deleteName = '';
    public string $search = '';
    public int $perPage = 5;

    #[Title('Kelola Program Studi')]
    protected function rules(): array
    {
        $prodiId = $this->editId;

        return [
            'name' => 'required|string|max:255|unique:prodis,name,' . $prodiId,
            'code' => 'required|string|max:20|unique:prodis,code,' . $prodiId,
            'kaprodi_user_id' => 'nullable|exists:users,id',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage($value): void
    {
        $allowed = [5, 10, 15, 20];
        $this->perPage = in_array((int) $value, $allowed, true) ? (int) $value : 5;
        $this->resetPage();
    }

    public function store(): void
    {
        $this->validate();

        $payload = [
            'name' => trim($this->name),
            'code' => strtoupper(trim($this->code)),
            'kaprodi_user_id' => $this->kaprodi_user_id !== '' ? (int) $this->kaprodi_user_id : null,
        ];

        if ($payload['kaprodi_user_id']) {
            Prodi::query()
                ->where('kaprodi_user_id', $payload['kaprodi_user_id'])
                ->when($this->editId, fn($query) => $query->where('id', '!=', $this->editId))
                ->update(['kaprodi_user_id' => null]);
        }

        if ($this->editId) {
            Prodi::findOrFail($this->editId)->update($payload);
            $this->dispatch('notify', message: 'Program studi berhasil diperbarui.');
        } else {
            Prodi::create($payload);
            $this->dispatch('notify', message: 'Program studi berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function edit(int $id): void
    {
        $this->resetErrorBag();
        $prodi = Prodi::findOrFail($id);

        $this->editId = $prodi->id;
        $this->name = $prodi->name;
        $this->code = $prodi->code;
        $this->kaprodi_user_id = (string) ($prodi->kaprodi_user_id ?? '');

        $this->dispatch('open-modal', name: 'prodi');
    }

    public function confirmDelete(int $id): void
    {
        $prodi = Prodi::findOrFail($id);
        $this->deleteId = $prodi->id;
        $this->deleteName = $prodi->name;
        $this->dispatch('open-modal', name: 'delete-prodi');
    }

    public function delete(): void
    {
        $prodi = Prodi::findOrFail($this->deleteId);

        if ($prodi->mahasiswas()->exists()) {
            $this->dispatch('notify', message: 'Program studi tidak bisa dihapus karena masih dipakai mahasiswa.');
            return;
        }

        $prodi->delete();
        $this->dispatch('close-modal', name: 'delete-prodi');
        $this->dispatch('notify', message: 'Program studi berhasil dihapus.');
        $this->reset(['deleteId', 'deleteName']);
    }

    public function closeModal(): void
    {
        $this->resetValidation();
        $this->resetErrorBag();
        $this->reset(['name', 'code', 'kaprodi_user_id', 'editId']);
        $this->dispatch('close-modal', name: 'prodi');
    }

    public function render()
    {
        $prodiList = Prodi::query()
            ->with(['kaprodiUser', 'mahasiswas'])
            ->when($this->search !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', '%' . $search . '%')
                        ->orWhere('code', 'like', '%' . $search . '%')
                        ->orWhereHas('kaprodiUser', fn($userQuery) => $userQuery->where('name', 'like', '%' . $search . '%'));
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.pages.prodi-management', [
            'prodiList' => $prodiList,
            'kaprodiUsers' => User::role('kaprodi')->orderBy('name')->get(),
        ]);
    }
}
