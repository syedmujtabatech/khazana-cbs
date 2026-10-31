<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountProduct extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
        'currency',
        'interest_rate',
        'minimum_balance',
        'daily_withdrawal_limit',
        'tenor_months',
        'early_withdrawal_penalty',
        'description',
        'is_active',
    ];

    protected $casts = [
        'interest_rate' => 'decimal:2',
        'minimum_balance' => 'decimal:2',
        'daily_withdrawal_limit' => 'decimal:2',
        'early_withdrawal_penalty' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function accounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }
}
