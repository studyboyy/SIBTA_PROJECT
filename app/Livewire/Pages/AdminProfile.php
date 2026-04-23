<?php

namespace App\Livewire\Pages;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

class AdminProfile extends Component
{
    use WithFileUploads;

    #[Title('Profil Admin')]
    public string $name = '';
    public string $email = '';
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';
    public $photo;
    public ?string $oldPhoto = null;

    public function mount(): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->name = '';
            $this->email = '';

            return;
        }

        $this->name = $user->name;
        $this->email = $user->email;
        $this->oldPhoto = $user->photo;
    }

    public function saveProfile(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        if (! empty($validated['photo'])) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }

            $filename = now()->format('YmdHis')
                . '-admin-' . Str::slug($validated['name'])
                . '.' . $validated['photo']->getClientOriginalExtension();

            $validated['photo'] = $validated['photo']->storeAs('avatar_users', $filename, 'public');
            $this->oldPhoto = $validated['photo'];
            $this->photo = null;
        } else {
            unset($validated['photo']);
        }

        $user->update($validated);

        $this->dispatch('notify', message: 'Profil admin berhasil diperbarui');
    }

    public function updatePassword(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        $user->update([
            'password' => $validated['password'],
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        $this->dispatch('notify', message: 'Password admin berhasil diperbarui');
    }

    public function render()
    {
        return view('livewire.pages.admin-profile');
    }
}
