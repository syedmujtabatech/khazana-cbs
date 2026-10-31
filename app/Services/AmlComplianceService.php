<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\CustomerProfile;
use App\Models\Transaction;
use App\Models\SecurityAuditLog;
use Carbon\Carbon;

class AmlComplianceService
{
    // State Bank of Pakistan (SBP) Cash Transaction Report (CTR) threshold: PKR 2.0 Million
    const SBP_CTR_THRESHOLD = 2000000.00;

    // Daily High-Velocity Aggregation Threshold: PKR 5.0 Million
    const DAILY_VELOCITY_LIMIT = 5000000.00;

    /**
     * Evaluate transaction for Anti-Money Laundering (AML) / Countering Financing of Terrorism (CFT) flags.
     * Returns array with: ['flagged' => bool, 'reason' => string|null, 'ctr_reported' => bool, 'requires_checker' => bool]
     */
    public function evaluateTransaction(BankAccount $account, string $type, float $amount, ?string $channel = 'teller'): array
    {
        $result = [
            'flagged' => false,
            'reason' => null,
            'ctr_reported' => false,
            'requires_checker' => false,
        ];

        $customer = $account->customer;

        // 1. SBP Mandatory CTR Threshold Check (Cash Deposit or Cash Withdrawal >= 2M PKR)
        if (in_array($type, ['cash_deposit', 'cash_withdrawal']) && $amount >= self::SBP_CTR_THRESHOLD) {
            $result['ctr_reported'] = true;
            $result['flagged'] = true;
            $result['reason'] = 'SBP CTR Rule: Single cash transaction >= PKR 2,000,000 threshold exceeded.';
            $result['requires_checker'] = true;
        }

        // 2. Politically Exposed Person (PEP) or High Risk Profile
        if ($customer && ($customer->is_pep || $customer->risk_rating === 'high' || $customer->risk_rating === 'pep')) {
            $result['flagged'] = true;
            $pepNote = $customer->is_pep ? 'PEP Customer Profile' : 'High-Risk Customer Profile';
            $result['reason'] = ($result['reason'] ? $result['reason'] . ' | ' : '') . "Enhanced Due Diligence triggered for {$pepNote}.";
            $result['requires_checker'] = true;
        }

        // 3. 24-Hour Velocity Check
        $past24HoursTotal = Transaction::where(function($q) use ($account) {
                $q->where('source_account_id', $account->id)
                  ->orWhere('destination_account_id', $account->id);
            })
            ->where('status', 'posted')
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->sum('amount');

        if (($past24HoursTotal + $amount) >= self::DAILY_VELOCITY_LIMIT) {
            $result['flagged'] = true;
            $formattedTotal = number_format($past24HoursTotal + $amount, 2);
            $result['reason'] = ($result['reason'] ? $result['reason'] . ' | ' : '') . "Velocity Alert: Cumulative 24h volume (PKR {$formattedTotal}) exceeds PKR 5M threshold.";
            $result['requires_checker'] = true;
        }

        // 4. Any amount >= PKR 100,000 requires maker-checker approval by standard bank policy
        if ($amount >= 100000.00) {
            $result['requires_checker'] = true;
        }

        return $result;
    }

    /**
     * Log AML/Forensic Event
     */
    public function logComplianceEvent(string $eventType, string $description, ?int $userId = null, ?array $payload = null, string $severity = 'warning'): void
    {
        SecurityAuditLog::create([
            'user_id' => $userId,
            'event_type' => $eventType,
            'severity' => $severity,
            'ip_address' => request()->ip() ?? '127.0.0.1',
            'user_agent' => request()->userAgent() ?? 'Khazana-CBS/1.0 Engine',
            'description' => $description,
            'payload' => $payload,
        ]);
    }
}
