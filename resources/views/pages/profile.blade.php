<?php

use Illuminate\Support\Facades\Hash;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component
{
    use Toast;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount()
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function save()
    {
        $user = auth()->user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ];

        if ($this->password) {
            $rules['password'] = 'required|min:8|confirmed';
        }

        $this->validate($rules);

        $user->name = $this->name;
        $user->email = $this->email;

        if ($this->password) {
            $user->password = Hash::make($this->password);
        }

        $user->save();

        $this->password = '';
        $this->password_confirmation = '';

        $this->success('Perfil actualizado correctamente.');
    }
}; ?>

<div>
    <x-header title="Mi Perfil" subtitle="Gestiona tu información personal" separator />

    <div class="grid gap-5 lg:grid-cols-2">
        <x-card shadow>
            <x-form wire:submit="save">
                <x-input label="Nombre" wire:model="name" icon="o-user" />
                <x-input label="Email" wire:model="email" icon="o-envelope" />
                
                <x-menu-separator class="my-4" />
                <div class="text-sm font-semibold mb-2">Cambiar Contraseña (opcional)</div>
                
                <x-input label="Nueva Contraseña" wire:model="password" type="password" icon="o-key" placeholder="Dejar en blanco para no cambiar" />
                <x-input label="Confirmar Contraseña" wire:model="password_confirmation" type="password" icon="o-key" />

                <x-slot:actions>
                    <x-button label="Guardar Cambios" type="submit" icon="o-check" class="btn-primary" spinner="save" />
                </x-slot:actions>
            </x-form>
        </x-card>
        
        <div>
            <x-card title="Información de Cuenta" shadow>
                <div class="flex flex-col gap-4">
                    <div class="flex justify-between items-center border-b pb-2">
                        <span class="text-sm opacity-60 text-base-content/50">Rol actual:</span>
                        <x-badge :value="auth()->user()->role" :class="auth()->user()->isAdmin() ? 'badge-primary' : 'badge-ghost'" />
                    </div>
                    <div class="flex justify-between items-center border-b pb-2">
                        <span class="text-sm opacity-60 text-base-content/50">Miembro desde:</span>
                        <span class="font-medium">{{ auth()->user()->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="alert alert-info shadow-sm mt-4">
                        <x-icon name="o-information-circle" class="w-6 h-6" />
                        <span class="text-xs">Los usuarios con rol estándar no pueden cambiar su propio rol. Si necesitas privilegios adicionales, contacta con un administrador.</span>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
