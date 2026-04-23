<?php

namespace App\Livewire\Components;

use App\Livewire\Auth\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Sidebar extends Component
{
    

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect(route('login'));
    }
  
    public function render()
    {
        return view('livewire.components.sidebar');
    }
}
