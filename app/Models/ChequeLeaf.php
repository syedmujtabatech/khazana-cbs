<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChequeLeaf extends Model
{
    protected $fillable = [
        'cheque_book_id',
        'bank_account_id',
        'leaf_number',
        'amount',
        'payee_name',
        'status',
        'bounce_reason',
        'cleared_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'cleared_at' => 'datetime',
    ];

    public function chequeBook(): BelongsTo
    {
        return $this->belongsTo(ChequeBook::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }
}
