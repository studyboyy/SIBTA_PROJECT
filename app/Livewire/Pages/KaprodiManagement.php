<?php

namespace App\Livewire\Pages;

use App\Models\Prodi;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class KaprodiManagement extends Component
{
    use WithPagination;

    public string $name = '';
    public string $email = '';
    public string $prodi_id = '';
    public ?int $editId = null;
    public ?int $deleteId = null;
    public ?int $resetId = null;
    public ?string $resetEmail = null;
    public string $deleteName = '';
    public string $search = '';
    public int $perPage = 5;

    #[Title('Kelola Kaprodi')]
    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->editId,
            'prodi_id' => 'nullable|exists:prodis,id',
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

        DB::transaction(function () {
            if ($this->editId) {
                $user = User::findOrFail($this->editId);
                $user->update([
                    'name' => trim($this->name),
                    'email' => trim($this->email),
                ]);
            } else {
                $user = User::create([
                    'name' => trim($this->name),
                    'email' => trim($this->email),
                    'password' => Hash::make('Kaprodi123!'),
                ]);
                $user->syncRoles(['kaprodi']);
            }

            Prodi::query()->where('kaprodi_user_id', $user->id)->update(['kaprodi_user_id' => null]);

            if ($this->prodi_id !== '') {
                Prodi::query()->where('id', (int) $this->prodi_id)->update(['kaprodi_user_id' => $user->id]);
            }
        });

        $this->dispatch('notify', message: $this->editId ? 'Data kaprodi berhasil diperbarui.' : 'Kaprodi berhasil ditambahkan.');
        $this->closeModal();
    }

    public function edit(int $id): void
    {
        $this->resetErrorBag();
        $user = User::with('managedProdi')->findOrFail($id);

        $this->editId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->prodi_id = (string) ($user->managedProdi?->id ?? '');

        $this->dispatch('open-modal', name: 'kaprodi');
    }

    public function confirmDelete(int $id): void
    {
        $user = User::findOrFail($id);
        $this->deleteId = $user->id;
        $this->deleteName = $user->name;
        $this->dispatch('open-modal', name: 'delete-kaprodi');
    }

    public function delete(): void
    {
        DB::transaction(function () {
            $user = User::findOrFail($this->deleteId);
            Prodi::query()->where('kaprodi_user_id', $user->id)->update(['kaprodi_user_id' => null]);
            $user->delete();
        });

        $this->dispatch('close-modal', name: 'delete-kaprodi');
        $this->dispatch('notify', message: 'Data kaprodi berhasil dihapus.');
        $this->reset(['deleteId', 'deleteName']);
    }

    public function confirmResetPassword(int $id): void
    {
        $user = User::findOrFail($id);
        $this->resetId = $user->id;
        $this->resetEmail = $user->email;
        $this->dispatch('open-modal', name: 'reset-kaprodi-password');
    }

    public function resetPassword(): void
    {
        User::findOrFail($this->resetId)->update([
            'password' => 'Kaprodi123!',
        ]);

        $this->dispatch('close-modal', name: 'reset-kaprodi-password');
        $this->dispatch('notify', message: 'Password baru untuk ' . ($this->resetEmail ?? '-') . ': Kaprodi123!');
        $this->reset(['resetId', 'resetEmail']);
    }

    public function closeModal(): void
    {
        $this->resetValidation();
        $this->resetErrorBag();
        $this->reset(['name', 'email', 'prodi_id', 'editId']);
        $this->dispatch('close-modal', name: 'kaprodi');
    }

    public function render()
    {
        $kaprodiList = User::role('kaprodi')
            ->with('managedProdi')
            ->when($this->search !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhereHas('managedProdi', fn($prodiQuery) => $prodiQuery->where('name', 'like', '%' . $search . '%'));
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.pages.kaprodi-management', [
            'kaprodiList' => $kaprodiList,
            'availableProdis' => Prodi::query()->orderBy('name')->get(),
        ]);
    }
}
