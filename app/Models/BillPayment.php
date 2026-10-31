<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillPayment extends Model
{
    protected $fillable = [
        'biller_id',
        'bank_account_id',
        'user_id',
        'consumer_number',
        'amount',
        'late_surcharge',
        'due_date',
        'billing_month',
        'transaction_reference',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'late_surcharge' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function biller(): BelongsTo
    {
        return $this->belongsTo(Biller::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
