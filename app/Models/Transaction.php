<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_reference',
        'source_account_id',
        'destination_account_id',
        'branch_id',
        'maker_id',
        'checker_id',
        'transaction_type',
        'channel',
        'currency',
        'amount',
        'fee',
        'tax',
        'net_amount',
        'narration',
        'external_bank_name',
        'external_iban',
        'external_beneficiary_name',
        'cheque_number',
        'status',
        'is_aml_flagged',
        'aml_reason',
        'ctr_reported',
        'posted_at',
        'authorized_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'tax' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'is_aml_flagged' => 'boolean',
        'ctr_reported' => 'boolean',
        'posted_at' => 'datetime',
        'authorized_at' => 'datetime',
    ];

    public function sourceAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'source_account_id');
    }

    public function destinationAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'destination_account_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function maker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'maker_id');
    }

    public function checker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checker_id');
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(GeneralLedgerEntry::class);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'posted' => 'bg-label-success',
            'pending_approval' => 'bg-label-warning',
            'rejected' => 'bg-label-danger',
            'reversed' => 'bg-label-secondary',
            default => 'bg-label-primary'
        };
    }
}
