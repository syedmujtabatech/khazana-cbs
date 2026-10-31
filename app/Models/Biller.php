<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Biller extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
        'consumer_number_label',
        'consumer_number_length',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(BillPayment::class);
    }
}
