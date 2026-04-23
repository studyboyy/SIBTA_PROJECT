<?php

namespace App\Livewire\Components;

use Livewire\Attributes\On;
use Livewire\Component;

class Modal extends Component
{
    public $isOpen = false;

    

    #[On("openModal")]

    // public function openModal()
    // {
    //     if ($this->isOpen &&  $this->namemodal) {
    //         $this->isOpen = true;
    //     }
    // }
    #[On("closeModal")]

    public function closeModal()
    {
        $this->isOpen = false;
    }
    public function render()
    {
        return view('livewire.components.modal');
    }
}
