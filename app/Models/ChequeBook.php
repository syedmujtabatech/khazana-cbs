<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChequeBook extends Model
{
    protected $fillable = [
        'bank_account_id',
        'book_number',
        'start_leaf',
        'end_leaf',
        'total_leaves',
        'status',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(ChequeLeaf::class);
    }
}
