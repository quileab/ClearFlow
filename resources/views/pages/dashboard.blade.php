<?php

use App\Models\DailyBalance;
use App\Models\Movement;
use Carbon\Carbon;
use Livewire\Volt\Component;

new class extends Component
{
    public float $cashBalance = 0;

    public float $bankBalance = 0;

            public string $fromDate = '';
            public string $toDate = '';
        
            public array $healthChart = [];
            public array $efficiencyChart = [];
            public array $liquidityChart = [];
            public array $annualEvolutionChart = [];
        
            public function mount()
            {
                // Default range: current month
                $this->fromDate = Carbon::now()->startOfMonth()->toDateString();
                $this->toDate = Carbon::now()->endOfMonth()->toDateString();
        
                $this->refreshData();
            }
        
            public function updatedFromDate()
            {
                $this->refreshData();
            }
        
            public function updatedToDate()
            {
                $this->refreshData();
            }
        
            public function refreshData()
            {
                $this->calculateBalances();
                $this->calculateHealthChart();
                $this->calculateEfficiencyChart();
                $this->calculateLiquidityChart();
                $this->calculateAnnualEvolutionChart();
            }
        
                protected function getRangeDates(): array
                {
                    $start = Carbon::parse($this->fromDate ?: Carbon::now()->startOfMonth()->toDateString());
                    $end = Carbon::parse($this->toDate ?: Carbon::now()->endOfMonth()->toDateString());
            
                    // Ensure start is not after end to prevent calculation errors
                    if ($start->gt($end)) {
                        $temp = $start;
                        $start = $end;
                        $end = $temp;
                        
                        // Sync properties back to UI if they were swapped
                        $this->fromDate = $start->toDateString();
                        $this->toDate = $end->toDateString();
                    }
            
                    return [$start, $end];
                }
                

    public function calculateBalances()
    {
        [$start, $end] = $this->getRangeDates();

        // For simplicity in this demo, we calculate from all movements + initial opening balance
        $initialBalance = DailyBalance::orderBy('date', 'asc')->first();

        $openingCash = $initialBalance ? $initialBalance->opening_balance_cash : 0;
        $openingBank = $initialBalance ? $initialBalance->opening_balance_bank : 0;

        // Sum everything BEFORE the range to get a starting point for THIS range
        $prevIncomeCash = Movement::whereHas('category', fn ($q) => $q->where('type', 'income'))
            ->where('method', 'cash')
            ->where('date', '<', $start->toDateString())
            ->sum('amount');

        $prevExpenseCash = Movement::whereHas('category', fn ($q) => $q->where('type', 'expense'))
            ->where('method', 'cash')
            ->where('date', '<', $start->toDateString())
            ->sum('amount');

        $prevIncomeBank = Movement::whereHas('category', fn ($q) => $q->where('type', 'income'))
            ->where('method', 'bank')
            ->where('date', '<', $start->toDateString())
            ->sum('amount');

        $prevExpenseBank = Movement::whereHas('category', fn ($q) => $q->where('type', 'expense'))
            ->where('method', 'bank')
            ->where('date', '<', $start->toDateString())
            ->sum('amount');

        // Sum current range
        $currentIncomeCash = Movement::whereHas('category', fn ($q) => $q->where('type', 'income'))
            ->where('method', 'cash')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->sum('amount');

        $currentExpenseCash = Movement::whereHas('category', fn ($q) => $q->where('type', 'expense'))
            ->where('method', 'cash')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->sum('amount');

        $currentIncomeBank = Movement::whereHas('category', fn ($q) => $q->where('type', 'income'))
            ->where('method', 'bank')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->sum('amount');

        $currentExpenseBank = Movement::whereHas('category', fn ($q) => $q->where('type', 'expense'))
            ->where('method', 'bank')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->sum('amount');

        $this->cashBalance = $openingCash + $prevIncomeCash - $prevExpenseCash + $currentIncomeCash - $currentExpenseCash;
        $this->bankBalance = $openingBank + $prevIncomeBank - $prevExpenseBank + $currentIncomeBank - $currentExpenseBank;
    }

    public function calculateHealthChart()
    {
        [$start, $end] = $this->getRangeDates();
        $daysInRange = $start->diffInDays($end) + 1;

        $labels = [];
        $incomeData = array_fill(0, $daysInRange, 0);
        $expenseData = array_fill(0, $daysInRange, 0);

        $movements = Movement::with('category')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get();

        for ($i = 0; $i < $daysInRange; $i++) {
            $labels[] = $start->copy()->addDays($i)->format('d/m');
        }

        foreach ($movements as $m) {
            $dayIndex = $start->diffInDays($m->date);
            $amount = (float) $m->amount;

            if ($dayIndex >= 0 && $dayIndex < $daysInRange) {
                if ($m->category->type === 'income') {
                    $incomeData[$dayIndex] += $amount;
                } else {
                    $expenseData[$dayIndex] += $amount;
                }
            }
        }

        $this->healthChart = [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Ingresos',
                        'data' => $incomeData,
                        'backgroundColor' => '#22c55e',
                    ],
                    [
                        'label' => 'Egresos',
                        'data' => $expenseData,
                        'backgroundColor' => '#ef4444',
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'display' => true,
                    ],
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                    ],
                ],
            ],
        ];
    }

    public function calculateEfficiencyChart()
    {
        [$start, $end] = $this->getRangeDates();

        $fixed = Movement::whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->whereHas('category', fn ($q) => $q->where('type', 'expense')->where('classification', 'fixed'))
            ->sum('amount');

        $variable = Movement::whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->whereHas('category', fn ($q) => $q->where('type', 'expense')->where('classification', 'variable'))
            ->sum('amount');

        $this->efficiencyChart = [
            'type' => 'doughnut',
            'data' => [
                'labels' => ['Costos Fijos', 'Costos Variables'],
                'datasets' => [
                    [
                        'data' => [$fixed, $variable],
                        'backgroundColor' => ['#3b82f6', '#f59e0b'],
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'display' => true,
                    ],
                ],
            ],
        ];
    }

    public function calculateLiquidityChart()
    {
        [$start, $end] = $this->getRangeDates();
        $daysInRange = $start->diffInDays($end) + 1;
        $labels = [];
        $cashData = [];
        $bankData = [];

        // Get the initial balance before the start date
        $initialBalance = DailyBalance::orderBy('date', 'asc')->first();
        $runningCash = $initialBalance?->opening_balance_cash ?? 0;
        $runningBank = $initialBalance?->opening_balance_bank ?? 0;

        // Sum all movements BEFORE start to get the running balance at start
        $prevMovements = Movement::with('category')
            ->where('date', '<', $start->toDateString())
            ->get();

        foreach ($prevMovements as $m) {
            $amount = (float) $m->amount;
            $isIncome = $m->category->type === 'income';
            if ($m->method === 'cash') {
                $runningCash += $isIncome ? $amount : -$amount;
            } else {
                $runningBank += $isIncome ? $amount : -$amount;
            }
        }

        // Get all movements in the current range
        $movements = Movement::with('category')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date', 'asc')
            ->get()
            ->groupBy(fn ($m) => $m->date->toDateString());

        for ($i = 0; $i < $daysInRange; $i++) {
            $currentDate = $start->copy()->addDays($i);
            $dateString = $currentDate->toDateString();
            $labels[] = $currentDate->format('d/m');

            if (isset($movements[$dateString])) {
                foreach ($movements[$dateString] as $m) {
                    $amount = (float) $m->amount;
                    $isIncome = $m->category->type === 'income';
                    if ($m->method === 'cash') {
                        $runningCash += $isIncome ? $amount : -$amount;
                    } else {
                        $runningBank += $isIncome ? $amount : -$amount;
                    }
                }
            }

            $cashData[] = $runningCash;
            $bankData[] = $runningBank;
        }

        $this->liquidityChart = [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Banco',
                        'data' => $bankData,
                        'backgroundColor' => 'rgba(14, 165, 233, 0.5)',
                        'borderColor' => '#0ea5e9',
                        'fill' => true,
                    ],
                    [
                        'label' => 'Efectivo',
                        'data' => $cashData,
                        'backgroundColor' => 'rgba(107, 114, 128, 0.5)',
                        'borderColor' => '#6b7280',
                        'fill' => true,
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'display' => true,
                    ],
                ],
                'elements' => [
                    'line' => [
                        'tension' => 0.4,
                    ],
                ],
                'scales' => [
                    'y' => [
                        'stacked' => true,
                    ],
                ],
            ],
        ];
    }

    public function calculateAnnualEvolutionChart()
    {
        [$rangeStart, $rangeEnd] = $this->getRangeDates();

        // Always show the last 12 months relative to the END of the selected range
        $end = $rangeEnd->copy();
        $start = $end->copy()->subMonths(11)->startOfMonth();

        $labels = [];
        $netData = array_fill(0, 12, 0);

        $movements = Movement::with('category')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get();

        for ($i = 11; $i >= 0; $i--) {
            $date = $end->copy()->subMonths($i);
            $labels[] = $date->translatedFormat('M Y');
        }

        foreach ($movements as $m) {
            $mDate = $m->date;
            $monthsDiff = ($end->year * 12 + $end->month) - ($mDate->year * 12 + $mDate->month);

            if ($monthsDiff >= 0 && $monthsDiff < 12) {
                $amount = (float) $m->amount;
                $isIncome = $m->category->type === 'income';
                $index = 11 - $monthsDiff;
                $netData[$index] += $isIncome ? $amount : -$amount;
            }
        }

        $this->annualEvolutionChart = [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Resultado Neto',
                        'data' => $netData,
                        'borderColor' => '#10b981',
                        'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'display' => true,
                    ],
                ],
            ],
        ];
    }
}; ?>

<div>
    <x-header title="Dashboard" subtitle="Resumen de saldos actuales" separator>
        <x-slot:actions>
            <div class="flex items-center gap-2">
                <x-datepicker wire:model.live="fromDate" icon="o-calendar" class="input-sm" placeholder="Desde" />
                <span class="text-xs opacity-50">al</span>
                <x-datepicker wire:model.live="toDate" icon="o-calendar" class="input-sm" placeholder="Hasta" />
            </div>
        </x-slot:actions>
    </x-header>

    <div class="grid gap-5 lg:grid-cols-2">
        <x-stat
            title="Saldos en Caja (Efectivo)"
            description="Total acumulado"
            :value="number_format($cashBalance, 2, ',', '.')"
            icon="o-banknotes"
            :color="$cashBalance >= 0 ? 'text-success' : 'text-error'"
            class="{{ $cashBalance >= 0 ? 'text-success' : 'text-error' }}"
            tooltip="Efectivo disponible en caja" />

        <x-stat
            title="Saldos en Banco"
            description="Total acumulado"
            :value="number_format($bankBalance, 2, ',', '.')"
            icon="o-credit-card"
            :color="$bankBalance >= 0 ? 'text-success' : 'text-error'"
            class="{{ $bankBalance >= 0 ? 'text-success' : 'text-error' }}"
            tooltip="Saldo disponible en cuentas bancarias" />
    </div>

    <div class="grid gap-8 mt-10 lg:grid-cols-1">
        <x-card title="Termómetro de Salud (Ingresos vs. Egresos)" subtitle="Comparativa diaria del mes actual" shadow>
            <div class="h-80">
                <x-chart wire:model="healthChart" class="h-full" />
            </div>
        </x-card>
    </div>

    <div class="grid gap-8 mt-8 lg:grid-cols-2">
        <x-card title="Radar de Eficiencia" subtitle="Gastos Fijos vs. Variables (Mes Actual)" shadow>
            <div class="h-64">
                <x-chart wire:model="efficiencyChart" class="h-full" />
            </div>
        </x-card>

        <x-card title="Indicador de Liquidez" subtitle="Distribución de dinero (Últimos 30 días)" shadow>
            <div class="h-64">
                <x-chart wire:model="liquidityChart" class="h-full" />
            </div>
        </x-card>
    </div>

    <div class="grid gap-8 mt-8 lg:grid-cols-1">
        <x-card title="Evolución Anual de Resultados" subtitle="Tendencia neta de los últimos 12 meses" shadow>
            <div class="h-80">
                <x-chart wire:model="annualEvolutionChart" class="h-full" />
            </div>
        </x-card>
    </div>

    <div class="mt-10 mb-10">
        <x-card title="Acciones Rápidas" shadow>
            <div class="flex gap-4">
                <x-button label="Nuevo Movimiento" icon="o-plus" class="btn-primary" link="/movements" />
                <x-button label="Ver Reportes" icon="o-chart-pie" class="btn-outline" link="/reports" />
            </div>
        </x-card>
    </div>
</div>
