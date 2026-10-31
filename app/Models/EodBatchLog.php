<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EodBatchLog extends Model
{
    protected $fillable = [
        'batch_reference',
        'business_date',
        'triggered_by',
        'accounts_processed',
        'total_profit_accrued',
        'tdr_matured_amount',
        'gl_balanced',
        'vault_discrepancy',
        'status',
        'execution_summary',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'business_date' => 'date',
        'total_profit_accrued' => 'decimal:2',
        'tdr_matured_amount' => 'decimal:2',
        'gl_balanced' => 'boolean',
        'vault_discrepancy' => 'decimal:2',
        'execution_summary' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function triggerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }
}
