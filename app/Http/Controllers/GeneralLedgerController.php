<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChartOfAccount;
use App\Models\GeneralLedgerEntry;
use App\Models\Transaction;
use App\Services\BankingLedgerService;
use Illuminate\Support\Facades\Auth;

class GeneralLedgerController extends Controller
{
    protected BankingLedgerService $ledgerService;

    public function __construct(BankingLedgerService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    public function chartOfAccounts()
    {
        $accounts = ChartOfAccount::orderBy('account_code')->get()->groupBy('account_type');
        return view('cbs.gl.chart_of_accounts', compact('accounts'));
    }

    public function trialBalance()
    {
        $glAccounts = ChartOfAccount::orderBy('account_code')->get();

        $totalDebits = 0;
        $totalCredits = 0;

        foreach ($glAccounts as $gl) {
            $debits = GeneralLedgerEntry::where('chart_of_account_id', $gl->id)
                ->where('entry_type', 'debit')
                ->sum('amount');
            $credits = GeneralLedgerEntry::where('chart_of_account_id', $gl->id)
                ->where('entry_type', 'credit')
                ->sum('amount');

            $gl->period_debits = $debits;
            $gl->period_credits = $credits;

            $totalDebits += $debits;
            $totalCredits += $credits;
        }

        $isBalanced = abs($totalDebits - $totalCredits) < 0.01;

        return view('cbs.gl.trial_balance', compact('glAccounts', 'totalDebits', 'totalCredits', 'isBalanced'));
    }

    public function journalEntries(Request $request)
    {
        $query = GeneralLedgerEntry::with(['account', 'branch', 'creator']);

        if ($request->filled('batch')) {
            $query->where('batch_reference', 'like', "%{$request->batch}%");
        }

        if ($request->filled('account_id')) {
            $query->where('chart_of_account_id', $request->account_id);
        }

        $entries = $query->latest()->paginate(25);
        $glAccounts = ChartOfAccount::all();

        return view('cbs.gl.journal_entries', compact('entries', 'glAccounts'));
    }

    public function reverseTransaction(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $transaction = Transaction::findOrFail($id);
        $user = Auth::user();

        if (!$user->isChecker()) {
            return back()->with('error', 'Only Branch Managers and Super Admins can execute Storno Reversals.');
        }

        try {
            $this->ledgerService->stornoReverse($transaction, $user, $validated['reason']);
            return back()->with('success', "Transaction {$transaction->transaction_reference} reversed via Storno accounting entry.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
