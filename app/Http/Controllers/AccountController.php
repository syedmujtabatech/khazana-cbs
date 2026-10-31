<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankAccount;
use App\Models\CustomerProfile;
use App\Models\AccountProduct;
use App\Models\Branch;
use App\Models\Transaction;
use App\Services\IbanGeneratorService;
use App\Services\BankingLedgerService;
use App\Services\MakerCheckerService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AccountController extends Controller
{
    protected BankingLedgerService $ledgerService;
    protected MakerCheckerService $makerCheckerService;

    public function __construct(BankingLedgerService $ledgerService, MakerCheckerService $makerCheckerService)
    {
        $this->ledgerService = $ledgerService;
        $this->makerCheckerService = $makerCheckerService;
    }

    public function index(Request $request)
    {
        $query = BankAccount::with(['customer', 'branch', 'product']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('account_number', 'like', "%{$s}%")
                  ->orWhere('iban', 'like', "%{$s}%")
                  ->orWhere('account_title', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $accounts = $query->latest()->paginate(10);
        $branches = Branch::all();
        $products = AccountProduct::all();

        return view('cbs.accounts.index', compact('accounts', 'branches', 'products'));
    }

    public function create(Request $request)
    {
        $customers = CustomerProfile::orderBy('first_name')->get();
        $branches = Branch::where('status', 'active')->get();
        $products = AccountProduct::where('is_active', true)->get();
        $selectedCustomer = $request->filled('customer_id') ? CustomerProfile::find($request->customer_id) : null;

        return view('cbs.accounts.create', compact('customers', 'branches', 'products', 'selectedCustomer'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_profile_id' => 'required|exists:customer_profiles,id',
            'branch_id' => 'required|exists:branches,id',
            'account_product_id' => 'required|exists:account_products,id',
            'initial_deposit' => 'required|numeric|min:0',
        ]);

        $customer = CustomerProfile::findOrFail($validated['customer_profile_id']);
        $branch = Branch::findOrFail($validated['branch_id']);
        $product = AccountProduct::findOrFail($validated['account_product_id']);

        // Generate 14-digit local account number and 24-char SBP IBAN
        $nextSeq = BankAccount::count() + 100205;
        $accNumber = IbanGeneratorService::generateAccountNumber($branch->code, $nextSeq);
        $iban = IbanGeneratorService::generateIban($branch->code, $accNumber);

        $account = BankAccount::create([
            'account_number' => $accNumber,
            'iban' => $iban,
            'customer_profile_id' => $customer->id,
            'branch_id' => $branch->id,
            'account_product_id' => $product->id,
            'account_title' => $customer->full_name . ' - ' . $product->name,
            'currency' => $product->currency,
            'available_balance' => 0.00,
            'ledger_balance' => 0.00,
            'status' => 'active',
            'opened_at' => Carbon::now(),
        ]);

        // Process initial cash deposit if > 0
        if ($validated['initial_deposit'] > 0) {
            $user = Auth::user();
            $this->ledgerService->postCashDeposit(
                $account,
                (float)$validated['initial_deposit'],
                $user,
                "Initial Opening Deposit for A/C {$account->account_number}"
            );
        }

        return redirect()->route('cbs.accounts.show', $account->id)->with('success', "Account {$account->account_number} created with IBAN {$account->iban}!");
    }

    public function show($id)
    {
        $account = BankAccount::with(['customer', 'branch', 'product', 'debitCards', 'chequeBooks.leaves', 'loans'])->findOrFail($id);
        
        $transactions = Transaction::where(function($q) use ($account) {
            $q->where('source_account_id', $account->id)
              ->orWhere('destination_account_id', $account->id);
        })->latest()->take(20)->get();

        return view('cbs.accounts.show', compact('account', 'transactions'));
    }

    public function toggleFreeze(Request $request, $id)
    {
        $account = BankAccount::findOrFail($id);
        $user = Auth::user();
        $targetAction = ($account->status === 'frozen') ? 'account_unfreeze' : 'account_freeze';

        // Check if user is Checker/Admin (can direct action) or Maker (submits maker-checker request)
        if ($user->isChecker()) {
            if ($account->status === 'frozen') {
                $account->update(['status' => 'active', 'frozen_at' => null]);
                $msg = "Account {$account->account_number} has been unfrozen successfully.";
            } else {
                $account->update(['status' => 'frozen', 'frozen_at' => Carbon::now()]);
                $msg = "Account {$account->account_number} has been frozen successfully.";
            }
            return back()->with('success', $msg);
        }

        // Maker initiates request
        $this->makerCheckerService->submitRequest(
            $user,
            $targetAction,
            0.00,
            ['bank_account_id' => $account->id, 'reason' => $request->input('reason', 'Administrative action requested')],
            "Maker requested {$targetAction} for Account {$account->account_number}"
        );

        return back()->with('success', "Freeze/Unfreeze action submitted to Checker authorization queue.");
    }
}
