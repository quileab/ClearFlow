<?php

use Livewire\Volt\Component;
use App\Models\Movement;
use App\Models\Category;
use Carbon\Carbon;

new class extends Component
{
    public int $selectedYear;
    public int $selectedMonth;
    public string $view = 'monthly'; // 'monthly' or 'annual'

    public function mount()
    {
        $this->selectedYear = (int)date('Y');
        $this->selectedMonth = (int)date('m');
    }

    public function monthlyData(): array
    {
        $daysInMonth = Carbon::create($this->selectedYear, $this->selectedMonth)->daysInMonth;
        $data = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($this->selectedYear, $this->selectedMonth, $day)->format('Y-m-d');
            
            $income = Movement::whereDate('date', $date)
                ->whereHas('category', fn($q) => $q->where('type', 'income'))
                ->sum('amount');
                
            $expense = Movement::whereDate('date', $date)
                ->whereHas('category', fn($q) => $q->where('type', 'expense'))
                ->sum('amount');

            $data[] = [
                'day' => $day,
                'date' => $date,
                'income' => $income,
                'expense' => $expense,
                'balance' => $income - $expense
            ];
        }

        return $data;
    }

    public function annualData(): array
    {
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $income = Movement::whereYear('date', $this->selectedYear)
                ->whereMonth('date', $m)
                ->whereHas('category', fn($q) => $q->where('type', 'income'))
                ->sum('amount');
                
            $fixedExpense = Movement::whereYear('date', $this->selectedYear)
                ->whereMonth('date', $m)
                ->whereHas('category', fn($q) => $q->where('type', 'expense')->where('classification', 'fixed'))
                ->sum('amount');
                
            $variableExpense = Movement::whereYear('date', $this->selectedYear)
                ->whereMonth('date', $m)
                ->whereHas('category', fn($q) => $q->where('type', 'expense')->where('classification', 'variable'))
                ->sum('amount');

            $months[] = [
                'name' => Carbon::create()->month($m)->translatedFormat('F'),
                'income' => $income,
                'fixed' => $fixedExpense,
                'variable' => $variableExpense,
                'total_expense' => $fixedExpense + $variableExpense,
                'net' => $income - ($fixedExpense + $variableExpense)
            ];
        }
        return $months;
    }
}; ?>

<div>
    <x-header title="Reportes" subtitle="Análisis de flujo de fondos" separator>
        <x-slot:actions>
            <div class="join">
                <x-button label="Mensual" wire:click="$set('view', 'monthly')" :class="$view === 'monthly' ? 'btn-primary join-item' : 'join-item'" />
                <x-button label="Anual" wire:click="$set('view', 'annual')" :class="$view === 'annual' ? 'btn-primary join-item' : 'join-item'" />
            </div>
        </x-slot:actions>
    </x-header>

    <div class="flex gap-4 mb-6 items-end">
        <x-input wire:model.live="selectedYear" type="number" label="Año" inline class="w-24" />
        @if($view === 'monthly')
            <x-select wire:model.live="selectedMonth" :options="[
                ['id' => 1, 'name' => 'Enero'], ['id' => 2, 'name' => 'Febrero'], ['id' => 3, 'name' => 'Marzo'],
                ['id' => 4, 'name' => 'Abril'], ['id' => 5, 'name' => 'Mayo'], ['id' => 6, 'name' => 'Junio'],
                ['id' => 7, 'name' => 'Julio'], ['id' => 8, 'name' => 'Agosto'], ['id' => 9, 'name' => 'Septiembre'],
                ['id' => 10, 'name' => 'Octubre'], ['id' => 11, 'name' => 'Noviembre'], ['id' => 12, 'name' => 'Diciembre']
            ]" label="Mes" inline />
        @endif
    </div>

    <x-card shadow>
        @if($view === 'monthly')
            <x-table :rows="$this->monthlyData()" :headers="[
                ['key' => 'day', 'label' => 'Día'],
                ['key' => 'income', 'label' => 'Ingresos', 'class' => 'text-right'],
                ['key' => 'expense', 'label' => 'Egresos', 'class' => 'text-right'],
                ['key' => 'balance', 'label' => 'Resultado Neto', 'class' => 'text-right']
            ]">
                @scope('cell_income', $row)
                    <div class="text-right align-middle">
                        <span class="text-success">{{ $row['income'] > 0 ? '$'.number_format($row['income'], 2, ',', '.') : '-' }}</span>
                    </div>
                @endscope
                @scope('cell_expense', $row)
                    <div class="text-right align-middle">
                        <span class="text-error">{{ $row['expense'] > 0 ? '$'.number_format($row['expense'], 2, ',', '.') : '-' }}</span>
                    </div>
                @endscope
                @scope('cell_balance', $row)
                    <div class="text-right align-middle">
                        <span class="{{ $row['balance'] >= 0 ? 'text-success font-bold' : 'text-error font-bold' }}">
                            ${{ number_format($row['balance'], 2, ',', '.') }}
                        </span>
                    </div>
                @endscope
            </x-table>
        @else
            <x-table :rows="$this->annualData()" :headers="[
                ['key' => 'name', 'label' => 'Mes'],
                ['key' => 'income', 'label' => 'Ingresos', 'class' => 'text-right'],
                ['key' => 'fixed', 'label' => 'Gastos Fijos', 'class' => 'text-right'],
                ['key' => 'variable', 'label' => 'Gastos Variables', 'class' => 'text-right'],
                ['key' => 'net', 'label' => 'Resultado', 'class' => 'text-right']
            ]">
                @scope('cell_name', $row)
                    <span class="font-bold capitalize">{{ $row['name'] }}</span>
                @endscope
                @scope('cell_income', $row)
                    <div class="text-right">
                        <span class="text-success font-bold">${{ number_format($row['income'], 2, ',', '.') }}</span>
                    </div>
                @endscope
                @scope('cell_fixed', $row)
                    <div class="text-right">
                        <span class="text-error">${{ number_format($row['fixed'], 2, ',', '.') }}</span>
                    </div>
                @endscope
                @scope('cell_variable', $row)
                    <div class="text-right">
                        <span class="text-error">${{ number_format($row['variable'], 2, ',', '.') }}</span>
                    </div>
                @endscope
                @scope('cell_net', $row)
                    <div class="text-right">
                        <span class="{{ $row['net'] >= 0 ? 'bg-success/20 text-success' : 'bg-error/20 text-error' }} px-2 py-1 rounded font-bold">
                            ${{ number_format($row['net'], 2, ',', '.') }}
                        </span>
                    </div>
                @endscope
            </x-table>
        @endif
    </x-card>
</div>
