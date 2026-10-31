<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Beneficiary extends Model
{
    protected $fillable = [
        'user_id',
        'nick_name',
        'beneficiary_name',
        'transfer_type',
        'bank_name',
        'bank_code',
        'account_or_iban',
        'raast_id',
        'email',
        'phone',
        'is_verified',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
