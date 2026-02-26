<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyBalance extends Model
{
    /** @use HasFactory<\Database\Factories\DailyBalanceFactory> */
    use HasFactory;

    protected $fillable = [
        'date',
        'opening_balance_cash',
        'opening_balance_bank',
        'closing_balance_cash',
        'closing_balance_bank',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'opening_balance_cash' => 'decimal:2',
            'opening_balance_bank' => 'decimal:2',
            'closing_balance_cash' => 'decimal:2',
            'closing_balance_bank' => 'decimal:2',
        ];
    }
}
