<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChartOfAccount extends Model
{
    protected $fillable = [
        'account_code',
        'account_name',
        'account_type',
        'sub_category',
        'parent_id',
        'currency',
        'current_balance',
        'normal_balance',
        'is_reconciled',
        'status',
    ];

    protected $casts = [
        'current_balance' => 'decimal:2',
        'is_reconciled' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(GeneralLedgerEntry::class);
    }
}
