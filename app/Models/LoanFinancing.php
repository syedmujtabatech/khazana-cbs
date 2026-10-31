<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanFinancing extends Model
{
    protected $table = 'loans_financing';

    protected $fillable = [
        'loan_reference',
        'customer_profile_id',
        'bank_account_id',
        'product_type',
        'principal_amount',
        'markup_rate',
        'tenor_months',
        'monthly_installment',
        'total_payable',
        'remaining_balance',
        'installments_paid',
        'status',
        'disbursed_date',
        'maturity_date',
    ];

    protected $casts = [
        'principal_amount' => 'decimal:2',
        'markup_rate' => 'decimal:2',
        'monthly_installment' => 'decimal:2',
        'total_payable' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'disbursed_date' => 'date',
        'maturity_date' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class, 'customer_profile_id');
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }
}
