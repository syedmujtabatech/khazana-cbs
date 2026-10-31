<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MakerCheckerRequest extends Model
{
    protected $fillable = [
        'reference_no',
        'action_type',
        'maker_id',
        'checker_id',
        'branch_id',
        'amount',
        'currency',
        'payload',
        'status',
        'maker_notes',
        'checker_notes',
        'actioned_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payload' => 'array',
        'actioned_at' => 'datetime',
    ];

    public function maker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'maker_id');
    }

    public function checker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checker_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'approved' => 'bg-label-success',
            'pending' => 'bg-label-warning',
            'rejected' => 'bg-label-danger',
            'cancelled' => 'bg-label-secondary',
            default => 'bg-label-primary'
        };
    }
}
