<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Login extends Component
{
    public $email_nim, $password, $remember = false;

    public function mount(): void
    {
        $savedRemember = Cookie::get('sibta_login_remember');
        $savedIdentifier = Cookie::get('sibta_login_identifier');

        if ($savedRemember === '1' && $savedIdentifier) {
            $this->remember = true;
            $this->email_nim = $savedIdentifier;
        }
    }

    #[Title('Login')]
    #[Layout('layouts.auth')]

    public function login()
    {
        $this->validate([
            'email_nim' => 'required',
            'password' => 'required',
        ]);

        $login = trim((string) $this->email_nim);

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $attempted = Auth::attempt([
                'email' => $login,
                'password' => $this->password,
            ], (bool) $this->remember);
        } else {
            // NIM/NIDN disimpan di tabel profil role, jadi cari user via relasi terlebih dahulu.
            $user = User::query()
                ->where(function ($query) use ($login) {
                    $query->whereHas('mahasiswa', fn($mahasiswaQuery) => $mahasiswaQuery->where('nim', $login))
                        ->orWhereHas('dosen', fn($dosenQuery) => $dosenQuery->where('nidn', $login));
                })
                ->first();

            $attempted = $user
                ? Auth::attempt([
                    'email' => $user->email,
                    'password' => $this->password,
                ], (bool) $this->remember)
                : false;
        }

        if ($attempted) {
            if ((bool) $this->remember) {
                Cookie::queue('sibta_login_remember', '1', 60 * 24 * 30);
                Cookie::queue('sibta_login_identifier', $login, 60 * 24 * 30);
            } else {
                Cookie::queue(Cookie::forget('sibta_login_remember'));
                Cookie::queue(Cookie::forget('sibta_login_identifier'));
            }

            // regenerate session (security)
            request()->session()->regenerate();

            $user = Auth::user();

            if ($user->hasRole('admin')) {
                return redirect()->intended(route('dashboard'));
            }

            if ($user->hasRole('mahasiswa')) {
                return redirect()->intended(route('mahasiswa.dashboard'));
            }

            if ($user->hasRole('dosen')) {
                return redirect()->intended(route('dosen.dashboard'));
            }

            if ($user->hasRole('kaprodi') || $user->hasRole('pimpinan')) {
                return redirect()->intended(route('kaprodi.dashboard'));
            }

            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            $this->addError('email_nim', 'Role akun belum didukung.');

            return;
        }

        // kalau gagal
        $this->addError('email_nim', 'Email/NIM/NIDN atau password salah');

        // reset password saja (best practice)
        $this->reset('password');
    }
    public function render()
    {
        return view('livewire.auth.login');
    }
}
