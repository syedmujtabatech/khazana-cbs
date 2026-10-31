<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\GeneralLedgerEntry;
use App\Models\EodBatchLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EodBatchService
{
    /**
     * Run End-of-Day (EOD) System Settlement Batch
     */
    public function runEodBatch(User $triggeredBy, ?string $businessDate = null): EodBatchLog
    {
        $date = $businessDate ? Carbon::parse($businessDate) : Carbon::today();
        $batchRef = 'EOD-' . $date->format('Ymd') . '-' . rand(100, 999);
        $startedAt = Carbon::now();

        $accountsProcessed = 0;
        $totalProfitAccrued = 0.00;
        $tdrMaturedAmount = 0.00;
        $summary = [];

        DB::beginTransaction();
        try {
            // 1. Accrue Daily Profit on PLS Savings Accounts
            $savingsAccounts = BankAccount::whereHas('product', function($q) {
                $q->where('category', 'savings');
            })->where('status', 'active')->get();

            $profitExpenseGl = ChartOfAccount::where('account_code', BankingLedgerService::GL_SAVINGS_PROFIT_EXPENSE)->first();
            $savingsLiabilityGl = ChartOfAccount::where('account_code', BankingLedgerService::GL_CUSTOMER_SAVINGS)->first();

            foreach ($savingsAccounts as $acc) {
                $rate = $acc->product->interest_rate ?? 11.50; // default 11.5% PLS rate
                if ($acc->available_balance > 0 && $rate > 0) {
                    $dailyProfit = round(($acc->available_balance * ($rate / 100)) / 365, 2);
                    if ($dailyProfit > 0) {
                        $acc->increment('accrued_profit', $dailyProfit);
                        $totalProfitAccrued += $dailyProfit;

                        // Post GL Accrual Entry
                        if ($profitExpenseGl && $savingsLiabilityGl) {
                            GeneralLedgerEntry::create([
                                'batch_reference' => $batchRef,
                                'entry_number' => 1,
                                'chart_of_account_id' => $profitExpenseGl->id,
                                'branch_id' => $acc->branch_id,
                                'value_date' => $date->toDateString(),
                                'entry_type' => 'debit',
                                'amount' => $dailyProfit,
                                'currency' => $acc->currency,
                                'narration' => "EOD Daily Profit Accrual: A/C {$acc->account_number} @ {$rate}%",
                                'created_by' => $triggeredBy->id,
                            ]);
                            $profitExpenseGl->increment('current_balance', $dailyProfit);

                            GeneralLedgerEntry::create([
                                'batch_reference' => $batchRef,
                                'entry_number' => 2,
                                'chart_of_account_id' => $savingsLiabilityGl->id,
                                'branch_id' => $acc->branch_id,
                                'value_date' => $date->toDateString(),
                                'entry_type' => 'credit',
                                'amount' => $dailyProfit,
                                'currency' => $acc->currency,
                                'narration' => "EOD Customer Profit Accrued: A/C {$acc->account_number}",
                                'created_by' => $triggeredBy->id,
                            ]);
                            $savingsLiabilityGl->increment('current_balance', $dailyProfit);
                        }
                    }
                }
                $accountsProcessed++;
            }

            // 2. Process Matured TDRs (Term Deposit Receipts)
            $maturedTerms = BankAccount::whereHas('product', function($q) {
                $q->where('category', 'tdr');
            })->where('status', 'active')
              ->whereNotNull('tdr_maturity_date')
              ->where('tdr_maturity_date', '<=', $date->toDateString())
              ->get();

            foreach ($maturedTerms as $tdr) {
                if ($tdr->is_tdr_auto_rollover) {
                    // Auto-rollover: renew for same tenor
                    $tenor = $tdr->product->tenor_months ?? 12;
                    $tdr->update([
                        'tdr_maturity_date' => Carbon::parse($tdr->tdr_maturity_date)->addMonths($tenor),
                    ]);
                    $summary['auto_rollovers'][] = $tdr->account_number;
                } else {
                    // Liquidate TDR to available balance
                    $payout = $tdr->tdr_principal + $tdr->accrued_profit;
                    $tdr->increment('available_balance', $payout);
                    $tdr->update([
                        'tdr_principal' => 0.00,
                        'accrued_profit' => 0.00,
                    ]);
                    $tdrMaturedAmount += $payout;
                    $summary['liquidated_tdrs'][] = $tdr->account_number;
                }
            }

            // 3. General Ledger Balancing Check
            $totalDebits = GeneralLedgerEntry::where('entry_type', 'debit')->sum('amount');
            $totalCredits = GeneralLedgerEntry::where('entry_type', 'credit')->sum('amount');
            $glBalanced = (abs($totalDebits - $totalCredits) < 0.01);

            // 4. Branch Vault Reconciliation
            $branchesVaultSum = Branch::sum('current_vault_cash');
            $vaultGl = ChartOfAccount::where('account_code', BankingLedgerService::GL_VAULT_CASH)->first();
            $glVaultBalance = $vaultGl ? $vaultGl->current_balance : 0.00;
            $vaultDiscrepancy = round(abs($branchesVaultSum - $glVaultBalance), 2);

            $summary['total_savings_accounts_processed'] = $savingsAccounts->count();
            $summary['total_tdrs_checked'] = $maturedTerms->count();
            $summary['gl_total_debits'] = $totalDebits;
            $summary['gl_total_credits'] = $totalCredits;
            $summary['vault_gl_balance'] = $glVaultBalance;
            $summary['branches_vault_sum'] = $branchesVaultSum;

            $batchLog = EodBatchLog::create([
                'batch_reference' => $batchRef,
                'business_date' => $date->toDateString(),
                'triggered_by' => $triggeredBy->id,
                'accounts_processed' => $accountsProcessed,
                'total_profit_accrued' => $totalProfitAccrued,
                'tdr_matured_amount' => $tdrMaturedAmount,
                'gl_balanced' => $glBalanced,
                'vault_discrepancy' => $vaultDiscrepancy,
                'status' => 'completed',
                'execution_summary' => $summary,
                'started_at' => $startedAt,
                'completed_at' => Carbon::now(),
            ]);

            DB::commit();
            return $batchLog;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
