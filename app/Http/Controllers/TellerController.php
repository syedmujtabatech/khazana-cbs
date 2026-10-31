<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankAccount;
use App\Models\Branch;
use App\Models\Transaction;
use App\Models\ChequeLeaf;
use App\Services\BankingLedgerService;
use App\Services\MakerCheckerService;
use App\Services\AmlComplianceService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TellerController extends Controller
{
    protected BankingLedgerService $ledgerService;
    protected MakerCheckerService $makerCheckerService;
    protected AmlComplianceService $amlService;

    public function __construct(
        BankingLedgerService $ledgerService,
        MakerCheckerService $makerCheckerService,
        AmlComplianceService $amlService
    ) {
        $this->ledgerService = $ledgerService;
        $this->makerCheckerService = $makerCheckerService;
        $this->amlService = $amlService;
    }

    public function index()
    {
        $user = Auth::user();
        $branch = $user->branch ?? Branch::first();
        
        $todayTransactions = Transaction::where('branch_id', $branch->id)
            ->whereDate('created_at', Carbon::today())
            ->latest()
            ->take(10)
            ->get();

        $todayDeposits = Transaction::where('branch_id', $branch->id)
            ->whereDate('created_at', Carbon::today())
            ->where('transaction_type', 'cash_deposit')
            ->where('status', 'posted')
            ->sum('amount');

        $todayWithdrawals = Transaction::where('branch_id', $branch->id)
            ->whereDate('created_at', Carbon::today())
            ->where('transaction_type', 'cash_withdrawal')
            ->where('status', 'posted')
            ->sum('amount');

        return view('cbs.teller.index', compact('branch', 'todayTransactions', 'todayDeposits', 'todayWithdrawals'));
    }

    public function depositForm(Request $request)
    {
        $accounts = BankAccount::where('status', 'active')->with('customer')->get();
        $selectedAccount = $request->filled('account_id') ? BankAccount::with('customer')->find($request->account_id) : null;
        return view('cbs.teller.deposit', compact('accounts', 'selectedAccount'));
    }

    public function processDeposit(Request $request)
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'amount' => 'required|numeric|min:1',
            'narration' => 'nullable|string|max:255',
            'depositor_name' => 'nullable|string|max:100',
            'depositor_cnic' => 'nullable|string|max:20',
        ]);

        $account = BankAccount::findOrFail($validated['bank_account_id']);
        $amount = (float)$validated['amount'];
        $user = Auth::user();

        $aml = $this->amlService->evaluateTransaction($account, 'cash_deposit', $amount);

        // If transaction requires checker approval (> 100k or AML rule)
        if ($aml['requires_checker'] && !$user->isChecker()) {
            $this->makerCheckerService->submitRequest(
                $user,
                'cash_deposit',
                $amount,
                [
                    'bank_account_id' => $account->id,
                    'amount' => $amount,
                    'narration' => $validated['narration'] ?? "Cash Deposit to A/C {$account->account_number}",
                    'depositor_name' => $validated['depositor_name'],
                    'depositor_cnic' => $validated['depositor_cnic'],
                    'currency' => $account->currency,
                ],
                $aml['reason'] ?? "High value cash deposit (> PKR 100,000) requiring Checker approval."
            );

            return redirect()->route('cbs.teller.index')->with('info', "High-value Cash Deposit of PKR " . number_format($amount, 2) . " forwarded to Checker queue for authorization.");
        }

        // Direct execution
        $txn = $this->ledgerService->postCashDeposit(
            $account,
            $amount,
            $user,
            $validated['narration'] ?? "Cash Deposit to A/C {$account->account_number}",
            $user->isChecker() ? $user : null
        );

        $msg = "Cash Deposit of PKR " . number_format($amount, 2) . " processed successfully! Ref: {$txn->transaction_reference}";
        if ($txn->ctr_reported) {
            $msg .= " [SBP CTR Alert: Threshold >= 2M reported].";
        }

        return redirect()->route('cbs.teller.index')->with('success', $msg);
    }

    public function withdrawalForm(Request $request)
    {
        $accounts = BankAccount::where('status', 'active')->with('customer')->get();
        $selectedAccount = $request->filled('account_id') ? BankAccount::with('customer')->find($request->account_id) : null;
        return view('cbs.teller.withdrawal', compact('accounts', 'selectedAccount'));
    }

    public function processWithdrawal(Request $request)
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'amount' => 'required|numeric|min:1',
            'narration' => 'nullable|string|max:255',
            'cheque_number' => 'nullable|string|max:30',
        ]);

        $account = BankAccount::findOrFail($validated['bank_account_id']);
        $amount = (float)$validated['amount'];
        $user = Auth::user();

        if ($account->available_balance < $amount) {
            return back()->with('error', "Insufficient account funds. Available: PKR " . number_format($account->available_balance, 2))->withInput();
        }

        $aml = $this->amlService->evaluateTransaction($account, 'cash_withdrawal', $amount);

        if ($aml['requires_checker'] && !$user->isChecker()) {
            $this->makerCheckerService->submitRequest(
                $user,
                'cash_withdrawal',
                $amount,
                [
                    'bank_account_id' => $account->id,
                    'amount' => $amount,
                    'narration' => $validated['narration'] ?? "Cash Withdrawal from A/C {$account->account_number}",
                    'cheque_number' => $validated['cheque_number'],
                    'currency' => $account->currency,
                ],
                $aml['reason'] ?? "High value cash withdrawal requiring Checker authorization."
            );

            return redirect()->route('cbs.teller.index')->with('info', "Withdrawal of PKR " . number_format($amount, 2) . " queued for Branch Manager / Checker approval.");
        }

        $txn = $this->ledgerService->postCashWithdrawal(
            $account,
            $amount,
            $user,
            $validated['narration'] ?? "Cash Withdrawal from A/C {$account->account_number}",
            $user->isChecker() ? $user : null
        );

        $msg = "Cash Withdrawal of PKR " . number_format($amount, 2) . " dispensed! Ref: {$txn->transaction_reference}";
        if ($txn->ctr_reported) {
            $msg .= " [SBP CTR Alert: Threshold >= 2M reported].";
        }

        return redirect()->route('cbs.teller.index')->with('success', $msg);
    }
}
