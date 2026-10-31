<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerProfile extends Model
{
    protected $fillable = [
        'cif_number',
        'user_id',
        'salutation',
        'first_name',
        'last_name',
        'father_or_husband_name',
        'mother_maiden_name',
        'cnic_passport',
        'cnic_expiry_date',
        'date_of_birth',
        'gender',
        'nationality',
        'occupation',
        'source_of_income',
        'monthly_income',
        'residential_address',
        'city',
        'country',
        'is_pep',
        'risk_rating',
        'kyc_status',
        'biometric_verified',
        'notes',
    ];

    protected $casts = [
        'cnic_expiry_date' => 'date',
        'date_of_birth' => 'date',
        'is_pep' => 'boolean',
        'biometric_verified' => 'boolean',
        'monthly_income' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    public function debitCards(): HasMany
    {
        return $this->hasMany(DebitCard::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(LoanFinancing::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->salutation} {$this->first_name} {$this->last_name}");
    }
}
