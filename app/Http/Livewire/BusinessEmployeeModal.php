<?php

namespace App\Http\Livewire;

use LivewireUI\Modal\ModalComponent;

class BusinessEmployeeModal extends ModalComponent
{
    public $name;
    public $email;
    public $role;

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'role' => 'required'
        ]);

        // Create employee logic here

        $this->closeModal();

        // Emit event to refresh the employee list
        $this->emit('employeeAdded');
    }

    public function render()
    {
        return view('livewire.business-employee-modal');
    }
}
