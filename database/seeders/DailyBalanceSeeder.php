<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\DailyBalance;
use Carbon\Carbon;

class DailyBalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DailyBalance::create([
            'date' => Carbon::now()->subMonths(36)->startOfMonth(),
            'opening_balance_cash' => 150000,
            'opening_balance_bank' => 500000,
            'closing_balance_cash' => 150000,
            'closing_balance_bank' => 500000,
        ]);
    }
}
