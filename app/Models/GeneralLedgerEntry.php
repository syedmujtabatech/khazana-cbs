<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralLedgerEntry extends Model
{
    protected $fillable = [
        'batch_reference',
        'entry_number',
        'chart_of_account_id',
        'branch_id',
        'transaction_id',
        'value_date',
        'entry_type',
        'amount',
        'currency',
        'narration',
        'is_reversal',
        'reversal_for_entry_id',
        'created_by',
    ];

    protected $casts = [
        'value_date' => 'date',
        'amount' => 'decimal:2',
        'is_reversal' => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
