<?php

namespace App\Livewire\Pages;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

class MahasiswaProfile extends Component
{
    use WithFileUploads;

    #[Title('Profil Mahasiswa')]
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
            return;
        }

        $this->name = $user->name;
        $this->email = $user->email;
        $this->oldPhoto = $user->mahasiswa?->photo;
    }

    public function saveProfile(): void
    {
        $user = Auth::user();
        $mahasiswa = $user?->mahasiswa;

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (! empty($validated['photo'])) {
            if ($mahasiswa?->photo) {
                Storage::disk('public')->delete($mahasiswa->photo);
            }

            $filename = now()->format('YmdHis')
                . '-mahasiswa-' . Str::slug($validated['name'])
                . '.' . $validated['photo']->getClientOriginalExtension();

            $path = $validated['photo']->storeAs('avatar_users', $filename, 'public');

            if ($mahasiswa) {
                $mahasiswa->update(['photo' => $path]);
            }

            $this->oldPhoto = $path;
            $this->photo = null;
        }

        $this->dispatch('notify', message: 'Profil mahasiswa berhasil diperbarui');
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

        $this->dispatch('notify', message: 'Password mahasiswa berhasil diperbarui');
    }

    public function render()
    {
        return view('livewire.pages.mahasiswa-profile', [
            'mahasiswa' => Auth::user()?->mahasiswa,
        ]);
    }
}
