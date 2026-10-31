<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = [
        'code',
        'name',
        'city',
        'province',
        'address',
        'phone',
        'email',
        'swift_bic',
        'routing_number',
        'is_head_office',
        'vault_cash_limit',
        'current_vault_cash',
        'status',
    ];

    protected $casts = [
        'is_head_office' => 'boolean',
        'vault_cash_limit' => 'decimal:2',
        'current_vault_cash' => 'decimal:2',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
