<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebitCard extends Model
{
    protected $fillable = [
        'bank_account_id',
        'customer_profile_id',
        'card_number_masked',
        'card_token',
        'card_type',
        'embossed_name',
        'expiry_month',
        'expiry_year',
        'cvv_hash',
        'pin_hash',
        'status',
        'is_ecommerce_enabled',
        'is_international_enabled',
        'is_contactless_enabled',
        'daily_atm_limit',
        'daily_pos_limit',
        'daily_ecom_limit',
        'issued_at',
        'blocked_at',
    ];

    protected $casts = [
        'is_ecommerce_enabled' => 'boolean',
        'is_international_enabled' => 'boolean',
        'is_contactless_enabled' => 'boolean',
        'daily_atm_limit' => 'decimal:2',
        'daily_pos_limit' => 'decimal:2',
        'daily_ecom_limit' => 'decimal:2',
        'issued_at' => 'datetime',
        'blocked_at' => 'datetime',
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class, 'customer_profile_id');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'active' => 'bg-label-success',
            'blocked' => 'bg-label-danger',
            'inactive' => 'bg-label-warning',
            default => 'bg-label-secondary'
        };
    }
}
