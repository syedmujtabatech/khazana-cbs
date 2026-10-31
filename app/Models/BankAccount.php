<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankAccount extends Model
{
    protected $fillable = [
        'account_number',
        'iban',
        'customer_profile_id',
        'branch_id',
        'account_product_id',
        'account_title',
        'currency',
        'available_balance',
        'ledger_balance',
        'hold_amount',
        'accrued_profit',
        'tdr_principal',
        'tdr_maturity_date',
        'is_tdr_auto_rollover',
        'status',
        'opened_at',
        'frozen_at',
        'dormant_at',
    ];

    protected $casts = [
        'available_balance' => 'decimal:2',
        'ledger_balance' => 'decimal:2',
        'hold_amount' => 'decimal:2',
        'accrued_profit' => 'decimal:2',
        'tdr_principal' => 'decimal:2',
        'tdr_maturity_date' => 'date',
        'is_tdr_auto_rollover' => 'boolean',
        'opened_at' => 'datetime',
        'frozen_at' => 'datetime',
        'dormant_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class, 'customer_profile_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(AccountProduct::class, 'account_product_id');
    }

    public function sentTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'source_account_id');
    }

    public function receivedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'destination_account_id');
    }

    public function debitCards(): HasMany
    {
        return $this->hasMany(DebitCard::class);
    }

    public function chequeBooks(): HasMany
    {
        return $this->hasMany(ChequeBook::class);
    }

    public function billPayments(): HasMany
    {
        return $this->hasMany(BillPayment::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(LoanFinancing::class);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'active' => 'bg-label-success',
            'frozen' => 'bg-label-danger',
            'dormant' => 'bg-label-warning',
            'closed' => 'bg-label-secondary',
            default => 'bg-label-primary'
        };
    }
}
