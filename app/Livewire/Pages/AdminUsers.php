<?php

namespace App\Livewire\Pages;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class AdminUsers extends Component
{
    use WithPagination;
    use WithoutUrlPagination;

    #[Title('User Admin')]
    public string $search = '';
    public int $perPage = 10;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public ?int $editId = null;
    public ?int $deleteId = null;
    public ?string $deleteName = null;
    public ?int $resetId = null;
    public ?string $resetEmail = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage($value): void
    {
        $allowed = [5, 10, 15, 20];
        $this->perPage = in_array((int) $value, $allowed, true) ? (int) $value : 10;
        $this->resetPage();
    }

    public function store(): void
    {
        $validated = $this->validate($this->rules(), $this->messages());

        DB::transaction(function () use ($validated) {
            if ($this->editId) {
                $admin = User::query()->role('admin')->findOrFail($this->editId);

                $admin->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ]);

                if (! empty($validated['password'])) {
                    $admin->update([
                        'password' => $validated['password'],
                    ]);
                }

                $message = 'User admin berhasil diperbarui';
            } else {
                $admin = User::query()->create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                ]);

                $admin->assignRole('admin');
                $message = 'User admin berhasil ditambahkan';
            }

            $this->dispatch('notify', message: $message);
        });

        $this->closeModal();
    }

    public function edit(int $id): void
    {
        $this->resetErrorBag();

        $admin = User::query()->role('admin')->findOrFail($id);

        $this->editId = $admin->id;
        $this->name = $admin->name;
        $this->email = $admin->email;
        $this->password = '';
        $this->password_confirmation = '';

        $this->dispatch('open-modal', name: 'admin-user');
    }

    public function confirmDelete(int $id): void
    {
        $admin = User::query()->role('admin')->findOrFail($id);

        $this->deleteId = $admin->id;
        $this->deleteName = $admin->name;

        $this->dispatch('open-modal', name: 'delete-admin-user');
    }

    public function delete(): void
    {
        $admin = User::query()->role('admin')->findOrFail($this->deleteId);

        if ($admin->id === Auth::id()) {
            $this->dispatch('notify', message: 'Akun admin yang sedang dipakai tidak bisa dihapus');
            $this->dispatch('close-modal', name: 'delete-admin-user');

            return;
        }

        if (User::query()->role('admin')->count() <= 1) {
            $this->dispatch('notify', message: 'Minimal harus ada satu user admin');
            $this->dispatch('close-modal', name: 'delete-admin-user');

            return;
        }

        $admin->delete();

        $this->dispatch('close-modal', name: 'delete-admin-user');
        $this->reset(['deleteId', 'deleteName']);
        $this->dispatch('notify', message: 'User admin berhasil dihapus');
        $this->resetPage();
    }

    public function confirmResetPassword(int $id): void
    {
        $admin = User::query()->role('admin')->findOrFail($id);

        $this->resetId = $admin->id;
        $this->resetEmail = $admin->email;

        $this->dispatch('open-modal', name: 'reset-admin-password');
    }

    public function resetPassword(): void
    {
        $admin = User::query()->role('admin')->findOrFail($this->resetId);

        $newPassword = Str::upper(Str::random(10));

        $admin->update([
            'password' => $newPassword,
        ]);

        $this->dispatch('close-modal', name: 'reset-admin-password');
        $this->reset(['resetId', 'resetEmail']);
        $this->dispatch('notify', message: 'Password baru untuk ' . $admin->email . ': ' . $newPassword);
    }

    public function closeModal(): void
    {
        $this->dispatch('close-modal', name: 'admin-user');
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'editId']);
        $this->resetErrorBag();
    }

    public function render()
    {
        $adminUsers = User::query()
            ->role('admin')
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.pages.admin-users', [
            'adminUsers' => $adminUsers,
        ]);
    }

    private function rules(): array
    {
        $passwordRules = $this->editId
            ? ['nullable', 'string', 'min:8', 'confirmed']
            : ['required', 'string', 'min:8', 'confirmed'];

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->editId)],
            'password' => $passwordRules,
        ];
    }

    private function messages(): array
    {
        return [
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ];
    }
}
