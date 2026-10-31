<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'branch_id',
        'phone',
        'transaction_pin',
        'two_factor_enabled',
        'two_factor_code',
        'status',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'transaction_pin',
        'two_factor_code',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function customerProfile(): HasOne
    {
        return $this->hasOne(CustomerProfile::class);
    }

    public function makerRequests(): HasMany
    {
        return $this->hasMany(MakerCheckerRequest::class, 'maker_id');
    }

    public function checkerRequests(): HasMany
    {
        return $this->hasMany(MakerCheckerRequest::class, 'checker_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(SecurityAuditLog::class);
    }

    public function beneficiaries(): HasMany
    {
        return $this->hasMany(Beneficiary::class);
    }

    // Role checking helpers
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isBranchManager(): bool
    {
        return $this->role === 'branch_manager';
    }

    public function isMaker(): bool
    {
        return in_array($this->role, ['maker_officer', 'branch_manager', 'super_admin']);
    }

    public function isChecker(): bool
    {
        return in_array($this->role, ['branch_manager', 'super_admin']);
    }

    public function isTeller(): bool
    {
        return in_array($this->role, ['teller', 'maker_officer', 'branch_manager', 'super_admin']);
    }

    public function isCompliance(): bool
    {
        return in_array($this->role, ['compliance_officer', 'super_admin']);
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function getRoleBadgeClassAttribute(): string
    {
        return match($this->role) {
            'super_admin' => 'bg-label-primary',
            'branch_manager' => 'bg-label-success',
            'maker_officer' => 'bg-label-info',
            'teller' => 'bg-label-warning',
            'compliance_officer' => 'bg-label-danger',
            'customer' => 'bg-label-secondary',
            default => 'bg-label-primary'
        };
    }
}
