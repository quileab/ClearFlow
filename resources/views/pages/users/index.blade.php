<?php

use Livewire\Volt\Component;
use App\Models\User;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Hash;
use Livewire\WithPagination;

new class extends Component
{
    use Toast, WithPagination;

    public string $search = '';
    
    // Form fields
    public bool $myDrawer = false;
    public ?int $user_id = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'user';

    public function mount()
    {
        if (!auth()->user()?->isAdmin()) {
            abort(403);
        }
    }

    public function users(): mixed
    {
        return User::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate(10);
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($this->user_id ?? 'NULL'),
            'role' => 'required|string|max:20',
        ];

        if (!$this->user_id) {
            $rules['password'] = 'required|min:8';
        }

        $data = $this->validate($rules);

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->user_id) {
            User::find($this->user_id)->update($data);
            $this->success('Usuario actualizado con éxito.');
        } else {
            User::create($data);
            $this->success('Usuario creado con éxito.');
        }

        $this->reset(['name', 'email', 'password', 'role', 'user_id', 'myDrawer']);
    }

    public function edit(User $user)
    {
        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->password = '';
        $this->myDrawer = true;
    }

    public function delete(User $user)
    {
        if ($user->id === auth()->id()) {
            $this->error('No puedes eliminarte a ti mismo.');
            return;
        }
        $user->delete();
        $this->success('Usuario eliminado.');
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-16'],
            ['key' => 'name', 'label' => 'Nombre'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'role', 'label' => 'Rol'],
        ];
    }
}; ?>

<div>
    <x-header title="Usuarios" subtitle="Administración de cuentas de acceso" separator>
        <x-slot:actions>
            <x-input placeholder="Buscar..." wire:model.live.debounce="search" icon="o-magnifying-glass" class="mr-2" />
            <x-button label="Nuevo Usuario" icon="o-plus" class="btn-primary" @click="$wire.myDrawer = true; $wire.user_id = null; $wire.name = ''; $wire.email = ''; $wire.password = ''; $wire.role = 'user';" />
        </x-slot:actions>
    </x-header>

    <x-card shadow>
        <x-table :headers="$this->headers()" :rows="$this->users()" with-pagination>
            @scope('cell_role', $user)
                <x-badge :value="$user->role" 
                         :class="$user->isAdmin() ? 'badge-primary' : 'badge-ghost'" />
            @endscope

            @scope('actions', $user)
                <div class="flex gap-2">
                    <x-button icon="o-pencil" wire:click="edit({{ $user->id }})" class="btn-ghost btn-sm text-info" />
                    <x-button icon="o-trash" wire:click="delete({{ $user->id }})" wire:confirm="¿Estás seguro?" class="btn-ghost btn-sm text-error" />
                </div>
            @endscope
        </x-table>
    </x-card>

    {{-- User Form Drawer --}}
    <x-drawer wire:model="myDrawer" :title="$user_id ? 'Editar Usuario' : 'Nuevo Usuario'" right separator with-close-button class="w-11/12 lg:w-1/3">
        <x-form wire:submit="save">
            <x-input label="Nombre" wire:model="name" icon="o-user" />
            <x-input label="Email" wire:model="email" icon="o-envelope" />
            <x-input label="Contraseña" wire:model="password" type="password" icon="o-key" :placeholder="$user_id ? 'Dejar en blanco para no cambiar' : ''" />
            <x-select label="Rol" wire:model="role" :options="[['id' => 'admin', 'name' => 'Admin'], ['id' => 'user', 'name' => 'Usuario']]" icon="o-identification" />

            <x-slot:actions>
                <x-button label="Cancelar" @click="$wire.myDrawer = false" />
                <x-button label="Guardar" type="submit" icon="o-check" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
