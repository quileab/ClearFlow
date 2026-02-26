<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Category;
use Mary\Traits\Toast;

new class extends Component
{
    use Toast, WithPagination;

    public string $search = '';

    public function categories(): mixed
    {
        return Category::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate(10);
    }

    public function delete(Category $category)
    {
        $category->delete();
        $this->success('Categoría eliminada con éxito.');
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-16'],
            ['key' => 'name', 'label' => 'Nombre'],
            ['key' => 'type', 'label' => 'Tipo'],
            ['key' => 'classification', 'label' => 'Clasificación'],
        ];
    }
}; ?>

<div>
    <x-header title="Categorías" subtitle="Gestión de naturalezas de movimientos" separator>
        <x-slot:actions>
            <x-input placeholder="Buscar..." wire:model.live.debounce="search" icon="o-magnifying-glass" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table :headers="$this->headers()" :rows="$this->categories()" with-pagination>
            @scope('cell_type', $category)
                <x-badge :value="$category['type'] === 'income' ? 'Ingreso' : 'Egreso'" 
                         :class="$category['type'] === 'income' ? 'badge-success' : 'badge-error'" />
            @endscope

            @scope('cell_classification', $category)
                <span class="capitalize">
                    {{ $category['classification'] === 'none' ? '-' : ($category['classification'] === 'fixed' ? 'Fijo' : 'Variable') }}
                </span>
            @endscope

            @scope('actions', $category)
                <x-button icon="o-trash" wire:click="delete({{ $category['id'] }})" wire:confirm="¿Estás seguro?" class="btn-ghost btn-sm text-error" />
            @endscope
        </x-table>
    </x-card>
</div>
