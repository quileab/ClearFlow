<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Category;
use App\Models\Movement;
use Carbon\Carbon;

class MovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $incomeCategories = $categories->where('type', 'income');
        $fixedExpenses = $categories->where('type', 'expense')->where('classification', 'fixed');
        $variableExpenses = $categories->where('type', 'expense')->where('classification', 'variable');

        $startDate = Carbon::now()->subMonths(36)->startOfMonth();
        $endDate = Carbon::now();

        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            // Simulate daily sales/income (2-5 per day)
            $incomeCount = rand(2, 5);
            for ($i = 0; $i < $incomeCount; $i++) {
                Movement::create([
                    'category_id' => $incomeCategories->random()->id,
                    'amount' => rand(5000, 25000),
                    'method' => rand(0, 1) ? 'cash' : 'bank',
                    'date' => $currentDate->copy(),
                    'description' => 'Venta diaria ' . ($i + 1),
                ]);
            }

            // Daily variable expenses (1-3 per day)
            $expenseCount = rand(1, 3);
            for ($i = 0; $i < $expenseCount; $i++) {
                Movement::create([
                    'category_id' => $variableExpenses->random()->id,
                    'amount' => rand(500, 5000),
                    'method' => rand(0, 1) ? 'cash' : 'bank',
                    'date' => $currentDate->copy(),
                    'description' => 'Gasto variable del día',
                ]);
            }

            // Fixed expenses once a month (around the 5th)
            if ($currentDate->day === 5) {
                foreach ($fixedExpenses as $fixed) {
                    Movement::create([
                        'category_id' => $fixed->id,
                        'amount' => $fixed->name === 'Alquiler Oficina' ? 80000 : rand(15000, 30000),
                        'method' => 'bank',
                        'date' => $currentDate->copy(),
                        'description' => 'Pago mensual de ' . $fixed->name,
                    ]);
                }
            }

            $currentDate->addDay();
        }
    }
}
