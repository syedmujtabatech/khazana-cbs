<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\GeneralLedgerEntry;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Biller;
use App\Models\BillPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;

class BankingLedgerService
{
    // Standard GL Codes
    const GL_VAULT_CASH = '1010';
    const GL_SBP_RESERVE = '1020';
    const GL_INTERBANK_CLEARING = '1030';
    const GL_CUSTOMER_CURRENT = '2010';
    const GL_CUSTOMER_SAVINGS = '2020';
    const GL_CUSTOMER_TDR = '2030';
    const GL_UTILITY_CLEARING = '2040';
    const GL_FEE_INCOME = '4010';
    const GL_FINANCING_MARKUP = '4020';
    const GL_SAVINGS_PROFIT_EXPENSE = '5010';

    protected AmlComplianceService $amlService;

    public function __construct(AmlComplianceService $amlService)
    {
        $this->amlService = $amlService;
    }

    /**
     * Helper to get GL account by code
     */
    private function getGlAccount(string $code): ChartOfAccount
    {
        $gl = ChartOfAccount::where('account_code', $code)->first();
        if (!$gl) {
            throw new Exception("Core General Ledger Account {$code} not configured.");
        }
        return $gl;
    }

    /**
     * Map customer bank account to corresponding customer liability GL
     */
    private function getCustomerLiabilityGl(BankAccount $account): ChartOfAccount
    {
        $category = $account->product ? $account->product->category : 'current';
        return match($category) {
            'savings' => $this->getGlAccount(self::GL_CUSTOMER_SAVINGS),
            'tdr' => $this->getGlAccount(self::GL_CUSTOMER_TDR),
            default => $this->getGlAccount(self::GL_CUSTOMER_CURRENT),
        };
    }

    /**
     * 1. Cash Deposit
     * Accounting:
     * DEBIT: Branch Vault Cash (Asset 1010) +Amount
     * CREDIT: Customer Deposit Account (Liability 2010/2020) +Amount
     */
    public function postCashDeposit(
        BankAccount $account,
        float $amount,
        User $maker,
        ?string $narration = null,
        ?User $checker = null
    ): Transaction {
        if ($amount <= 0) {
            throw new Exception("Deposit amount must be greater than zero.");
        }
        if ($account->status !== 'active') {
            throw new Exception("Account {$account->account_number} is {$account->status}. Deposits prohibited.");
        }

        $aml = $this->amlService->evaluateTransaction($account, 'cash_deposit', $amount);

        return DB::transaction(function() use ($account, $amount, $maker, $checker, $narration, $aml) {
            $txnRef = 'DEP-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            $batchRef = 'GLB-' . date('YmdHis') . '-' . rand(100, 999);
            $now = Carbon::now();

            $branch = $account->branch;
            $vaultGl = $this->getGlAccount(self::GL_VAULT_CASH);
            $customerGl = $this->getCustomerLiabilityGl($account);

            // 1. Create Transaction record
            $transaction = Transaction::create([
                'transaction_reference' => $txnRef,
                'source_account_id' => null, // Cash from counter
                'destination_account_id' => $account->id,
                'branch_id' => $branch->id,
                'maker_id' => $maker->id,
                'checker_id' => $checker?->id,
                'transaction_type' => 'cash_deposit',
                'channel' => 'teller',
                'currency' => $account->currency,
                'amount' => $amount,
                'fee' => 0.00,
                'tax' => 0.00,
                'net_amount' => $amount,
                'narration' => $narration ?? "Cash Deposit to A/C {$account->account_number}",
                'status' => 'posted',
                'is_aml_flagged' => $aml['flagged'],
                'aml_reason' => $aml['reason'],
                'ctr_reported' => $aml['ctr_reported'],
                'posted_at' => $now,
                'authorized_at' => $checker ? $now : $now,
            ]);

            // 2. Double Entry GL: Debit Vault Cash
            GeneralLedgerEntry::create([
                'batch_reference' => $batchRef,
                'entry_number' => 1,
                'chart_of_account_id' => $vaultGl->id,
                'branch_id' => $branch->id,
                'transaction_id' => $transaction->id,
                'value_date' => $now->toDateString(),
                'entry_type' => 'debit',
                'amount' => $amount,
                'currency' => $account->currency,
                'narration' => "Vault Cash in: Deposit for A/C {$account->account_number}",
                'created_by' => $maker->id,
            ]);
            $vaultGl->increment('current_balance', $amount);

            // 3. Double Entry GL: Credit Customer Deposit Liability
            GeneralLedgerEntry::create([
                'batch_reference' => $batchRef,
                'entry_number' => 2,
                'chart_of_account_id' => $customerGl->id,
                'branch_id' => $branch->id,
                'transaction_id' => $transaction->id,
                'value_date' => $now->toDateString(),
                'entry_type' => 'credit',
                'amount' => $amount,
                'currency' => $account->currency,
                'narration' => "Customer Deposit: Credit to A/C {$account->account_number}",
                'created_by' => $maker->id,
            ]);
            $customerGl->increment('current_balance', $amount);

            // 4. Update Bank Account Balances
            $account->increment('available_balance', $amount);
            $account->increment('ledger_balance', $amount);

            // 5. Update Branch Vault Cash
            $branch->increment('current_vault_cash', $amount);

            if ($aml['flagged']) {
                $this->amlService->logComplianceEvent(
                    'aml_ctr_alert',
                    "Cash Deposit flagged: {$aml['reason']} | Amount: PKR " . number_format($amount, 2),
                    $maker->id,
                    ['transaction_id' => $transaction->id, 'account_id' => $account->id]
                );
            }

            return $transaction;
        });
    }

    /**
     * 2. Cash Withdrawal
     * Accounting:
     * DEBIT: Customer Deposit Account (Liability 2010/2020) -Amount
     * CREDIT: Branch Vault Cash (Asset 1010) -Amount
     */
    public function postCashWithdrawal(
        BankAccount $account,
        float $amount,
        User $maker,
        ?string $narration = null,
        ?User $checker = null
    ): Transaction {
        if ($amount <= 0) {
            throw new Exception("Withdrawal amount must be greater than zero.");
        }
        if ($account->status !== 'active') {
            throw new Exception("Account {$account->account_number} is {$account->status}. Withdrawals prohibited.");
        }
        if ($account->available_balance < $amount) {
            throw new Exception("Insufficient funds. Available: PKR " . number_format($account->available_balance, 2));
        }

        $branch = $account->branch;
        if ($branch->current_vault_cash < $amount) {
            throw new Exception("Branch Vault Cash insufficient to dispense PKR " . number_format($amount, 2));
        }

        $aml = $this->amlService->evaluateTransaction($account, 'cash_withdrawal', $amount);

        return DB::transaction(function() use ($account, $amount, $maker, $checker, $narration, $branch, $aml) {
            $txnRef = 'WDL-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            $batchRef = 'GLB-' . date('YmdHis') . '-' . rand(100, 999);
            $now = Carbon::now();

            $vaultGl = $this->getGlAccount(self::GL_VAULT_CASH);
            $customerGl = $this->getCustomerLiabilityGl($account);

            $transaction = Transaction::create([
                'transaction_reference' => $txnRef,
                'source_account_id' => $account->id,
                'destination_account_id' => null, // Cash to customer
                'branch_id' => $branch->id,
                'maker_id' => $maker->id,
                'checker_id' => $checker?->id,
                'transaction_type' => 'cash_withdrawal',
                'channel' => 'teller',
                'currency' => $account->currency,
                'amount' => $amount,
                'fee' => 0.00,
                'tax' => 0.00,
                'net_amount' => $amount,
                'narration' => $narration ?? "Cash Withdrawal from A/C {$account->account_number}",
                'status' => 'posted',
                'is_aml_flagged' => $aml['flagged'],
                'aml_reason' => $aml['reason'],
                'ctr_reported' => $aml['ctr_reported'],
                'posted_at' => $now,
                'authorized_at' => $checker ? $now : $now,
            ]);

            // Double Entry GL: Debit Customer Liability (reduces liability)
            GeneralLedgerEntry::create([
                'batch_reference' => $batchRef,
                'entry_number' => 1,
                'chart_of_account_id' => $customerGl->id,
                'branch_id' => $branch->id,
                'transaction_id' => $transaction->id,
                'value_date' => $now->toDateString(),
                'entry_type' => 'debit',
                'amount' => $amount,
                'currency' => $account->currency,
                'narration' => "Customer Withdrawal: Debit from A/C {$account->account_number}",
                'created_by' => $maker->id,
            ]);
            $customerGl->decrement('current_balance', $amount);

            // Double Entry GL: Credit Vault Cash (reduces asset)
            GeneralLedgerEntry::create([
                'batch_reference' => $batchRef,
                'entry_number' => 2,
                'chart_of_account_id' => $vaultGl->id,
                'branch_id' => $branch->id,
                'transaction_id' => $transaction->id,
                'value_date' => $now->toDateString(),
                'entry_type' => 'credit',
                'amount' => $amount,
                'currency' => $account->currency,
                'narration' => "Vault Cash out: Dispense to A/C {$account->account_number}",
                'created_by' => $maker->id,
            ]);
            $vaultGl->decrement('current_balance', $amount);

            // Update Account Balances
            $account->decrement('available_balance', $amount);
            $account->decrement('ledger_balance', $amount);

            // Update Branch Vault
            $branch->decrement('current_vault_cash', $amount);

            if ($aml['flagged']) {
                $this->amlService->logComplianceEvent(
                    'aml_ctr_alert',
                    "Cash Withdrawal flagged: {$aml['reason']} | Amount: PKR " . number_format($amount, 2),
                    $maker->id,
                    ['transaction_id' => $transaction->id, 'account_id' => $account->id]
                );
            }

            return $transaction;
        });
    }

    /**
     * 3. Internal Account-to-Account Transfer
     * Accounting:
     * DEBIT: Source Account Liability
     * CREDIT: Destination Account Liability
     */
    public function postInternalTransfer(
        BankAccount $source,
        BankAccount $destination,
        float $amount,
        User $maker,
        ?string $narration = null,
        ?User $checker = null
    ): Transaction {
        if ($amount <= 0) {
            throw new Exception("Transfer amount must be positive.");
        }
        if ($source->id === $destination->id) {
            throw new Exception("Source and destination accounts cannot be the same.");
        }
        if ($source->status !== 'active') {
            throw new Exception("Source account is {$source->status}. Transfer rejected.");
        }
        if ($destination->status !== 'active') {
            throw new Exception("Destination account is {$destination->status}. Transfer rejected.");
        }
        if ($source->available_balance < $amount) {
            throw new Exception("Insufficient funds in source account.");
        }

        $aml = $this->amlService->evaluateTransaction($source, 'internal_transfer', $amount);

        return DB::transaction(function() use ($source, $destination, $amount, $maker, $checker, $narration, $aml) {
            $txnRef = 'TRF-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            $batchRef = 'GLB-' . date('YmdHis') . '-' . rand(100, 999);
            $now = Carbon::now();

            $sourceGl = $this->getCustomerLiabilityGl($source);
            $destGl = $this->getCustomerLiabilityGl($destination);

            $transaction = Transaction::create([
                'transaction_reference' => $txnRef,
                'source_account_id' => $source->id,
                'destination_account_id' => $destination->id,
                'branch_id' => $source->branch_id,
                'maker_id' => $maker->id,
                'checker_id' => $checker?->id,
                'transaction_type' => 'internal_transfer',
                'channel' => 'digital',
                'currency' => $source->currency,
                'amount' => $amount,
                'fee' => 0.00,
                'tax' => 0.00,
                'net_amount' => $amount,
                'narration' => $narration ?? "Transfer from {$source->account_number} to {$destination->account_number}",
                'status' => 'posted',
                'is_aml_flagged' => $aml['flagged'],
                'aml_reason' => $aml['reason'],
                'ctr_reported' => false,
                'posted_at' => $now,
                'authorized_at' => $now,
            ]);

            // GL Debit Source
            GeneralLedgerEntry::create([
                'batch_reference' => $batchRef,
                'entry_number' => 1,
                'chart_of_account_id' => $sourceGl->id,
                'branch_id' => $source->branch_id,
                'transaction_id' => $transaction->id,
                'value_date' => $now->toDateString(),
                'entry_type' => 'debit',
                'amount' => $amount,
                'currency' => $source->currency,
                'narration' => "Transfer Debit: From {$source->account_number}",
                'created_by' => $maker->id,
            ]);
            $sourceGl->decrement('current_balance', $amount);

            // GL Credit Destination
            GeneralLedgerEntry::create([
                'batch_reference' => $batchRef,
                'entry_number' => 2,
                'chart_of_account_id' => $destGl->id,
                'branch_id' => $destination->branch_id,
                'transaction_id' => $transaction->id,
                'value_date' => $now->toDateString(),
                'entry_type' => 'credit',
                'amount' => $amount,
                'currency' => $destination->currency,
                'narration' => "Transfer Credit: To {$destination->account_number}",
                'created_by' => $maker->id,
            ]);
            $destGl->increment('current_balance', $amount);

            // Balances
            $source->decrement('available_balance', $amount);
            $source->decrement('ledger_balance', $amount);

            $destination->increment('available_balance', $amount);
            $destination->increment('ledger_balance', $amount);

            return $transaction;
        });
    }

    /**
     * 4. Interbank Transfer (1LINK IBFT / Raast)
     * Accounting:
     * DEBIT: Source Customer Account (Amount + Fee)
     * CREDIT: 1LINK / SBP Clearing Account (Asset 1030) (Amount)
     * CREDIT: Commission & Fee Income (Revenue 4010) (Fee)
     */
    public function postInterbankTransfer(
        BankAccount $source,
        string $externalBank,
        string $externalIban,
        string $payeeName,
        float $amount,
        float $fee,
        User $maker,
        ?string $narration = null,
        string $channel = '1link_ibft'
    ): Transaction {
        $totalDebit = $amount + $fee;
        if ($source->available_balance < $totalDebit) {
            throw new Exception("Insufficient balance for IBFT transfer and charges.");
        }

        $aml = $this->amlService->evaluateTransaction($source, 'ibft_outward', $amount);

        return DB::transaction(function() use ($source, $externalBank, $externalIban, $payeeName, $amount, $fee, $totalDebit, $maker, $narration, $channel, $aml) {
            $txnRef = 'IBFT-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            $batchRef = 'GLB-' . date('YmdHis') . '-' . rand(100, 999);
            $now = Carbon::now();

            $sourceGl = $this->getCustomerLiabilityGl($source);
            $clearingGl = $this->getGlAccount(self::GL_INTERBANK_CLEARING);
            $feeGl = $this->getGlAccount(self::GL_FEE_INCOME);

            $transaction = Transaction::create([
                'transaction_reference' => $txnRef,
                'source_account_id' => $source->id,
                'destination_account_id' => null,
                'branch_id' => $source->branch_id,
                'maker_id' => $maker->id,
                'transaction_type' => $channel,
                'channel' => 'digital',
                'currency' => $source->currency,
                'amount' => $amount,
                'fee' => $fee,
                'tax' => 0.00,
                'net_amount' => $amount,
                'narration' => $narration ?? "IBFT Outward to {$payeeName} ({$externalBank})",
                'external_bank_name' => $externalBank,
                'external_iban' => $externalIban,
                'external_beneficiary_name' => $payeeName,
                'status' => 'posted',
                'is_aml_flagged' => $aml['flagged'],
                'aml_reason' => $aml['reason'],
                'posted_at' => $now,
                'authorized_at' => $now,
            ]);

            // GL Debit Source for total amount + fee
            GeneralLedgerEntry::create([
                'batch_reference' => $batchRef,
                'entry_number' => 1,
                'chart_of_account_id' => $sourceGl->id,
                'branch_id' => $source->branch_id,
                'transaction_id' => $transaction->id,
                'value_date' => $now->toDateString(),
                'entry_type' => 'debit',
                'amount' => $totalDebit,
                'currency' => $source->currency,
                'narration' => "IBFT Outward Debit: A/C {$source->account_number}",
                'created_by' => $maker->id,
            ]);
            $sourceGl->decrement('current_balance', $totalDebit);

            // GL Credit 1LINK Clearing for net transfer amount
            GeneralLedgerEntry::create([
                'batch_reference' => $batchRef,
                'entry_number' => 2,
                'chart_of_account_id' => $clearingGl->id,
                'branch_id' => $source->branch_id,
                'transaction_id' => $transaction->id,
                'value_date' => $now->toDateString(),
                'entry_type' => 'credit',
                'amount' => $amount,
                'currency' => $source->currency,
                'narration' => "IBFT Outward Clearing to {$externalBank} - {$externalIban}",
                'created_by' => $maker->id,
            ]);
            $clearingGl->decrement('current_balance', $amount);

            // GL Credit Fee Income (if fee > 0)
            if ($fee > 0) {
                GeneralLedgerEntry::create([
                    'batch_reference' => $batchRef,
                    'entry_number' => 3,
                    'chart_of_account_id' => $feeGl->id,
                    'branch_id' => $source->branch_id,
                    'transaction_id' => $transaction->id,
                    'value_date' => $now->toDateString(),
                    'entry_type' => 'credit',
                    'amount' => $fee,
                    'currency' => $source->currency,
                    'narration' => "IBFT Interbank Transfer Charges",
                    'created_by' => $maker->id,
                ]);
                $feeGl->increment('current_balance', $fee);
            }

            // Update Account Balance
            $source->decrement('available_balance', $totalDebit);
            $source->decrement('ledger_balance', $totalDebit);

            return $transaction;
        });
    }

    /**
     * 5. Bill Payment (1BILL)
     */
    public function postBillPayment(
        BankAccount $source,
        int $billerId,
        string $consumerNo,
        float $amount,
        float $fee,
        User $user
    ): BillPayment {
        $biller = Biller::findOrFail($billerId);
        $totalDebit = $amount + $fee;

        if ($source->available_balance < $totalDebit) {
            throw new Exception("Insufficient balance to pay bill of PKR " . number_format($totalDebit, 2));
        }

        return DB::transaction(function() use ($source, $biller, $consumerNo, $amount, $fee, $totalDebit, $user) {
            $txnRef = 'BILL-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            $batchRef = 'GLB-' . date('YmdHis') . '-' . rand(100, 999);
            $now = Carbon::now();

            $sourceGl = $this->getCustomerLiabilityGl($source);
            $utilityGl = $this->getGlAccount(self::GL_UTILITY_CLEARING);
            $feeGl = $this->getGlAccount(self::GL_FEE_INCOME);

            $transaction = Transaction::create([
                'transaction_reference' => $txnRef,
                'source_account_id' => $source->id,
                'destination_account_id' => null,
                'branch_id' => $source->branch_id,
                'maker_id' => $user->id,
                'transaction_type' => 'bill_payment',
                'channel' => 'digital',
                'currency' => $source->currency,
                'amount' => $amount,
                'fee' => $fee,
                'tax' => 0.00,
                'net_amount' => $amount,
                'narration' => "1BILL Payment: {$biller->name} (Consumer #{$consumerNo})",
                'status' => 'posted',
                'posted_at' => $now,
                'authorized_at' => $now,
            ]);

            // GL Debit Customer
            GeneralLedgerEntry::create([
                'batch_reference' => $batchRef,
                'entry_number' => 1,
                'chart_of_account_id' => $sourceGl->id,
                'branch_id' => $source->branch_id,
                'transaction_id' => $transaction->id,
                'value_date' => $now->toDateString(),
                'entry_type' => 'debit',
                'amount' => $totalDebit,
                'currency' => $source->currency,
                'narration' => "Bill Payment Debit: {$biller->name}",
                'created_by' => $user->id,
            ]);
            $sourceGl->decrement('current_balance', $totalDebit);

            // GL Credit Utility Clearing
            GeneralLedgerEntry::create([
                'batch_reference' => $batchRef,
                'entry_number' => 2,
                'chart_of_account_id' => $utilityGl->id,
                'branch_id' => $source->branch_id,
                'transaction_id' => $transaction->id,
                'value_date' => $now->toDateString(),
                'entry_type' => 'credit',
                'amount' => $amount,
                'currency' => $source->currency,
                'narration' => "Utility Payable: {$biller->name} #{$consumerNo}",
                'created_by' => $user->id,
            ]);
            $utilityGl->increment('current_balance', $amount);

            if ($fee > 0) {
                GeneralLedgerEntry::create([
                    'batch_reference' => $batchRef,
                    'entry_number' => 3,
                    'chart_of_account_id' => $feeGl->id,
                    'branch_id' => $source->branch_id,
                    'transaction_id' => $transaction->id,
                    'value_date' => $now->toDateString(),
                    'entry_type' => 'credit',
                    'amount' => $fee,
                    'currency' => $source->currency,
                    'narration' => "1BILL Convenience Fee",
                    'created_by' => $user->id,
                ]);
                $feeGl->increment('current_balance', $fee);
            }

            // Update account balance
            $source->decrement('available_balance', $totalDebit);
            $source->decrement('ledger_balance', $totalDebit);

            // Create Bill Payment log
            return BillPayment::create([
                'biller_id' => $biller->id,
                'bank_account_id' => $source->id,
                'user_id' => $user->id,
                'consumer_number' => $consumerNo,
                'amount' => $amount,
                'late_surcharge' => 0.00,
                'due_date' => Carbon::now()->addDays(5)->toDateString(),
                'billing_month' => Carbon::now()->format('M Y'),
                'transaction_reference' => $txnRef,
                'status' => 'paid',
                'paid_at' => $now,
            ]);
        });
    }

    /**
     * 6. Financial Reversal (Storno Principle)
     * SBP & GAAP rules: Never delete accounting lines. Post exact inverse debit/credits.
     */
    public function stornoReverse(Transaction $transaction, User $authorizer, string $reason): Transaction
    {
        if ($transaction->status === 'reversed') {
            throw new Exception("Transaction {$transaction->transaction_reference} has already been reversed.");
        }

        return DB::transaction(function() use ($transaction, $authorizer, $reason) {
            $batchRef = 'REV-' . date('YmdHis') . '-' . rand(100, 999);
            $now = Carbon::now();

            $existingEntries = GeneralLedgerEntry::where('transaction_id', $transaction->id)->get();

            foreach ($existingEntries as $entry) {
                // Invert type: debit becomes credit, credit becomes debit
                $inverseType = ($entry->entry_type === 'debit') ? 'credit' : 'debit';
                $glAccount = ChartOfAccount::find($entry->chart_of_account_id);

                GeneralLedgerEntry::create([
                    'batch_reference' => $batchRef,
                    'entry_number' => $entry->entry_number,
                    'chart_of_account_id' => $entry->chart_of_account_id,
                    'branch_id' => $entry->branch_id,
                    'transaction_id' => $transaction->id,
                    'value_date' => $now->toDateString(),
                    'entry_type' => $inverseType,
                    'amount' => $entry->amount,
                    'currency' => $entry->currency,
                    'narration' => "STORNO REVERSAL: {$reason} (Ref: {$transaction->transaction_reference})",
                    'is_reversal' => true,
                    'reversal_for_entry_id' => $entry->id,
                    'created_by' => $authorizer->id,
                ]);

                // Balance impact
                if ($inverseType === 'debit') {
                    $glAccount->increment('current_balance', $entry->amount);
                } else {
                    $glAccount->decrement('current_balance', $entry->amount);
                }
            }

            // Restore source account balance if any
            if ($transaction->source_account_id) {
                $source = BankAccount::find($transaction->source_account_id);
                $source->increment('available_balance', $transaction->amount + $transaction->fee);
                $source->increment('ledger_balance', $transaction->amount + $transaction->fee);
            }

            // Deduct destination account balance if any
            if ($transaction->destination_account_id) {
                $dest = BankAccount::find($transaction->destination_account_id);
                $dest->decrement('available_balance', $transaction->amount);
                $dest->decrement('ledger_balance', $transaction->amount);
            }

            // Restore branch vault cash if cash deposit or withdrawal
            if ($transaction->transaction_type === 'cash_deposit') {
                $branch = Branch::find($transaction->branch_id);
                $branch->decrement('current_vault_cash', $transaction->amount);
            } elseif ($transaction->transaction_type === 'cash_withdrawal') {
                $branch = Branch::find($transaction->branch_id);
                $branch->increment('current_vault_cash', $transaction->amount);
            }

            $transaction->update([
                'status' => 'reversed',
                'narration' => $transaction->narration . " [REVERSED by {$authorizer->name}: {$reason}]",
            ]);

            return $transaction;
        });
    }
}
