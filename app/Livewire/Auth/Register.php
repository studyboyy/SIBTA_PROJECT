<?php

namespace App\Livewire\Auth;

use App\Models\Mahasiswas;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

class Register extends Component
{
    use WithFileUploads;
    public $email, $name, $angkatan, $prodi,  $nim, $password, $password_confirmation, $photo;

    #[Title('Register')]
    #[Layout('layouts.auth')]
    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'nim' => 'required|string|max:255|unique:mahasiswas',
            'email' => 'required|string|email|max:255|unique:users',
            'angkatan' => 'required|string',
            'prodi' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);


        DB::transaction(function () {
            $pathPhoto = null;

            if ($this->photo) {
                $filename = now()->format('YmdHis')
                    . '-' . Str::slug($this->name)
                    . '.' . $this->photo->getClientOriginalExtension();

                $pathPhoto = $this->photo->storeAs('avatar_users', $filename, 'public');
            }
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);
            $user->assignRole('mahasiswa');

            Mahasiswas::create([
                'user_id' => $user->id,
                'nim' => $this->nim,
                'angkatan'=> $this->angkatan,
                'prodi' => $this->prodi,
                'photo' => $pathPhoto,
                'status_ta' => 'belum',
            ]);
        });





        $this->reset(['name', 'nim', 'email', 'password', 'password_confirmation']);

        return redirect(route('login'));
    }
    public function render()
    {
        return view('livewire.auth.register');
    }
}
