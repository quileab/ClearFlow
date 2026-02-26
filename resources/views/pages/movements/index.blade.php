<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Movement;
use App\Models\Category;
use Mary\Traits\Toast;
use Carbon\Carbon;

new class extends Component
{
    use Toast, WithPagination;

    public string $search = '';
    public ?string $typeFilter = null;
    public ?int $categoryFilter = null;
    
    // Form fields
    public bool $myDrawer = false;
    public ?int $category_id = null;
    public float $amount = 0;
    public string $method = 'cash';
    public string $date = '';
    public string $description = '';
    public string $selectedType = 'income';

    public function mount()
    {
        $this->date = date('Y-m-d');
    }

    public function updatedSelectedType()
    {
        $this->category_id = null;
    }

    public function movements(): mixed
    {
        return Movement::with('category')
            ->when($this->search, fn($q) => $q->where('description', 'like', "%{$this->search}%"))
            ->when($this->typeFilter, fn($q) => $q->whereHas('category', fn($c) => $c->where('type', $this->typeFilter)))
            ->when($this->categoryFilter, fn($q) => $q->where('category_id', $this->categoryFilter))
            ->orderBy('date', 'desc')
            ->paginate(10);
    }

    public function categories(): array
    {
        return Category::where('type', $this->selectedType)
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function save()
    {
        $data = $this->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,bank',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        Movement::create($data);
        
        $this->reset(['category_id', 'amount', 'description', 'myDrawer']);
        $this->success('Movimiento registrado con éxito.');
    }

    public function headers(): array
    {
        return [
            ['key' => 'date', 'label' => 'Fecha'],
            ['key' => 'category.name', 'label' => 'Categoría'],
            ['key' => 'method', 'label' => 'Método'],
            ['key' => 'amount', 'label' => 'Monto', 'class' => 'text-right'],
            ['key' => 'description', 'label' => 'Descripción'],
        ];
    }
}; ?>

<div>
    <x-header title="Movimientos" subtitle="Registro diario de ingresos y egresos" separator>
        <x-slot:actions>
            <x-button label="Nuevo" icon="o-plus" class="btn-primary" @click="$wire.myDrawer = true" />
        </x-slot:actions>
    </x-header>

    <div class="grid gap-4 mb-5 lg:grid-cols-4">
        <x-select label="Tipo" icon="o-funnel" wire:model.live="typeFilter" :options="[['id' => 'income', 'name' => 'Ingresos'], ['id' => 'expense', 'name' => 'Egresos']]" placeholder="Todos los tipos" />
        <x-input label="Buscar" icon="o-magnifying-glass" wire:model.live.debounce="search" placeholder="Descripción..." />
    </div>

    <x-card shadow>
        <x-table :headers="$this->headers()" :rows="$this->movements()" with-pagination>
            @scope('cell_date', $movement)
                {{ Carbon::parse($movement['date'])->format('d/m/Y') }}
            @endscope

            @scope('cell_method', $movement)
                <x-badge :value="$movement['method'] === 'cash' ? 'Efectivo' : 'Banco'" class="badge-ghost" />
            @endscope

            @scope('cell_amount', $movement)
                <div class="text-right">
                    <span class="{{ $movement['category']['type'] === 'income' ? 'text-success font-bold' : 'text-error' }}">
                        {{ $movement['category']['type'] === 'income' ? '+' : '-' }}
                        ${{ number_format($movement['amount'], 2, ',', '.') }}
                    </span>
                </div>
            @endscope
        </x-table>
    </x-card>

    <x-drawer wire:model="myDrawer" title="Nuevo Movimiento" right separator with-close-button class="w-11/12 lg:w-1/3">
        <x-form wire:submit="save">
            <div class="grid gap-6">
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold">Tipo de Movimiento</label>
                    <div class="join w-full">
                        <x-button 
                            label="Ingreso" 
                            icon="o-arrow-trending-up" 
                            class="join-item flex-1"
                            :class="$selectedType === 'income' ? 'btn-success text-white' : 'btn-ghost border-base-300'"
                            wire:click="$set('selectedType', 'income')" />
                        <x-button 
                            label="Egreso" 
                            icon="o-arrow-trending-down" 
                            class="join-item flex-1"
                            :class="$selectedType === 'expense' ? 'btn-error text-white' : 'btn-ghost border-base-300'"
                            wire:click="$set('selectedType', 'expense')" />
                    </div>
                </div>

                <x-select label="Categoría" wire:model="category_id" :options="$this->categories()" icon="o-tag" placeholder="Seleccione categoría" />
                
                <x-input label="Monto" wire:model="amount" prefix="$" type="number" step="0.01" icon="o-currency-dollar" />
                
                <x-select label="Método de Pago" wire:model="method" :options="[['id' => 'cash', 'name' => 'Efectivo'], ['id' => 'bank', 'name' => 'Banco']]" icon="o-credit-card" />
                
                <x-datepicker label="Fecha" wire:model="date" icon="o-calendar" />
                
                <x-textarea label="Descripción" wire:model="description" placeholder="Opcional..." rows="3" />
            </div>

            <x-slot:actions>
                <x-button label="Cancelar" @click="$wire.myDrawer = false" />
                <x-button label="Guardar" type="submit" icon="o-check" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
