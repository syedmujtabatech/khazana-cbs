<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankAccount;
use App\Models\Beneficiary;
use App\Models\Biller;
use App\Models\BillPayment;
use App\Models\DebitCard;
use App\Models\ChequeBook;
use App\Models\ChequeLeaf;
use App\Models\Transaction;
use App\Models\LoanFinancing;
use App\Services\BankingLedgerService;
use App\Services\MakerCheckerService;
use App\Services\AmlComplianceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CustomerPortalController extends Controller
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

    private function getCustomerPrimaryAccount()
    {
        $user = Auth::user();
        $profile = $user->customerProfile;
        if (!$profile) {
            abort(403, 'Customer profile not found for this user.');
        }
        return $profile->accounts()->first();
    }

    public function dashboard()
    {
        $user = Auth::user();
        $profile = $user->customerProfile;
        $accounts = $profile->accounts()->with('product')->get();
        $primaryAccount = $accounts->first();

        $debitCards = $profile->debitCards()->with('bankAccount')->get();
        $recentTransactions = collect();

        if ($primaryAccount) {
            $recentTransactions = Transaction::where(function($q) use ($primaryAccount) {
                $q->where('source_account_id', $primaryAccount->id)
                  ->orWhere('destination_account_id', $primaryAccount->id);
            })->latest()->take(6)->get();
        }

        $beneficiaries = $user->beneficiaries()->take(5)->get();
        $activeLoans = $profile->loans()->where('status', 'active')->get();

        return view('portal.dashboard', compact(
            'user',
            'profile',
            'accounts',
            'primaryAccount',
            'debitCards',
            'recentTransactions',
            'beneficiaries',
            'activeLoans'
        ));
    }

    public function statement(Request $request)
    {
        $user = Auth::user();
        $profile = $user->customerProfile;
        $accounts = $profile->accounts;
        
        $selectedAccount = $request->filled('account_id')
            ? $accounts->firstWhere('id', $request->account_id) ?? $accounts->first()
            : $accounts->first();

        $query = Transaction::where(function($q) use ($selectedAccount) {
            $q->where('source_account_id', $selectedAccount->id)
              ->orWhere('destination_account_id', $selectedAccount->id);
        })->where('status', 'posted');

        if ($request->filled('start_date')) {
            $query->whereDate('posted_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('posted_at', '<=', $request->end_date);
        }

        $transactions = $query->latest('posted_at')->paginate(20);

        return view('portal.statement', compact('profile', 'accounts', 'selectedAccount', 'transactions'));
    }

    public function transferForm()
    {
        $user = Auth::user();
        $profile = $user->customerProfile;
        $accounts = $profile->accounts()->where('status', 'active')->get();
        $beneficiaries = $user->beneficiaries;

        return view('portal.transfer', compact('accounts', 'beneficiaries'));
    }

    public function processTransfer(Request $request)
    {
        $validated = $request->validate([
            'source_account_id' => 'required|exists:bank_accounts,id',
            'transfer_type' => 'required|in:internal,1link_ibft,raast_instant',
            'amount' => 'required|numeric|min:10',
            'pin' => 'required|string',
            'narration' => 'nullable|string|max:100',
            
            // For internal transfer
            'dest_account_number' => 'required_if:transfer_type,internal|nullable|string',
            
            // For 1LINK IBFT
            'beneficiary_bank' => 'required_if:transfer_type,1link_ibft|nullable|string',
            'beneficiary_iban' => 'required_if:transfer_type,1link_ibft|nullable|string',
            'beneficiary_title' => 'required_if:transfer_type,1link_ibft|nullable|string',
            
            // For Raast
            'raast_id' => 'required_if:transfer_type,raast_instant|nullable|string',
        ]);

        $user = Auth::user();
        if (!Hash::check($validated['pin'], $user->transaction_pin)) {
            return back()->with('error', 'Invalid 4-digit Transaction Security PIN. Funds transfer declined.')->withInput();
        }

        $source = BankAccount::findOrFail($validated['source_account_id']);
        $amount = (float)$validated['amount'];

        if ($source->available_balance < $amount) {
            return back()->with('error', 'Insufficient funds in selected account.')->withInput();
        }

        try {
            if ($validated['transfer_type'] === 'internal') {
                $dest = BankAccount::where('account_number', $validated['dest_account_number'])
                    ->orWhere('iban', $validated['dest_account_number'])
                    ->first();

                if (!$dest) {
                    return back()->with('error', 'Destination Khazana Account or IBAN not found.')->withInput();
                }

                $txn = $this->ledgerService->postInternalTransfer(
                    $source,
                    $dest,
                    $amount,
                    $user,
                    $validated['narration'] ?? "Fund Transfer to A/C {$dest->account_number}"
                );
                $msg = "PKR " . number_format($amount, 2) . " transferred successfully to {$dest->account_title}! Ref: {$txn->transaction_reference}";

            } elseif ($validated['transfer_type'] === '1link_ibft') {
                $fee = 15.00; // 1LINK IBFT fee
                $txn = $this->ledgerService->postInterbankTransfer(
                    $source,
                    $validated['beneficiary_bank'],
                    $validated['beneficiary_iban'],
                    $validated['beneficiary_title'],
                    $amount,
                    $fee,
                    $user,
                    $validated['narration'] ?? "1LINK IBFT to {$validated['beneficiary_title']}",
                    '1link_ibft'
                );
                $msg = "1LINK IBFT of PKR " . number_format($amount, 2) . " sent to {$validated['beneficiary_title']} ({$validated['beneficiary_bank']})! Ref: {$txn->transaction_reference}";

            } else {
                // Raast Instant
                $txn = $this->ledgerService->postInterbankTransfer(
                    $source,
                    'SBP Raast Instant Ecosystem',
                    $validated['raast_id'],
                    'Raast Payee (' . $validated['raast_id'] . ')',
                    $amount,
                    0.00, // Raast has 0 fee
                    $user,
                    $validated['narration'] ?? "Raast P2P Instant Transfer",
                    'raast_instant'
                );
                $msg = "Raast Instant Transfer of PKR " . number_format($amount, 2) . " settled in real-time! Ref: {$txn->transaction_reference}";
            }

            return redirect()->route('portal.dashboard')->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function billPaymentForm()
    {
        $user = Auth::user();
        $profile = $user->customerProfile;
        $accounts = $profile->accounts()->where('status', 'active')->get();
        $billers = Biller::where('is_active', true)->get();
        $recentBills = BillPayment::where('user_id', $user->id)->with('biller')->latest()->take(5)->get();

        return view('portal.bill_payment', compact('accounts', 'billers', 'recentBills'));
    }

    public function processBillPayment(Request $request)
    {
        $validated = $request->validate([
            'source_account_id' => 'required|exists:bank_accounts,id',
            'biller_id' => 'required|exists:billers,id',
            'consumer_number' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'pin' => 'required|string',
        ]);

        $user = Auth::user();
        if (!Hash::check($validated['pin'], $user->transaction_pin)) {
            return back()->with('error', 'Invalid Transaction Security PIN.')->withInput();
        }

        $source = BankAccount::findOrFail($validated['source_account_id']);
        $amount = (float)$validated['amount'];

        try {
            $bill = $this->ledgerService->postBillPayment(
                $source,
                (int)$validated['biller_id'],
                $validated['consumer_number'],
                $amount,
                0.00,
                $user
            );

            return redirect()->route('portal.dashboard')->with('success', "Utility Bill of PKR " . number_format($amount, 2) . " paid successfully! Reference: {$bill->transaction_reference}");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function cardControls()
    {
        $user = Auth::user();
        $profile = $user->customerProfile;
        $cards = $profile->debitCards()->with('bankAccount')->get();

        return view('portal.card_controls', compact('cards'));
    }

    public function updateCard(Request $request, $id)
    {
        $user = Auth::user();
        $card = DebitCard::where('id', $id)
            ->where('customer_profile_id', $user->customerProfile->id)
            ->firstOrFail();

        $validated = $request->validate([
            'daily_atm_limit' => 'required|numeric|min:1000|max:500000',
            'daily_pos_limit' => 'required|numeric|min:1000|max:1000000',
            'daily_ecom_limit' => 'required|numeric|min:1000|max:500000',
        ]);

        $card->update([
            'is_ecommerce_enabled' => $request->boolean('is_ecommerce_enabled'),
            'is_international_enabled' => $request->boolean('is_international_enabled'),
            'daily_atm_limit' => $validated['daily_atm_limit'],
            'daily_pos_limit' => $validated['daily_pos_limit'],
            'daily_ecom_limit' => $validated['daily_ecom_limit'],
        ]);

        return back()->with('success', "Debit Card {$card->card_number_masked} preferences updated!");
    }

    public function toggleCardBlock($id)
    {
        $user = Auth::user();
        $card = DebitCard::where('id', $id)
            ->where('customer_profile_id', $user->customerProfile->id)
            ->firstOrFail();

        $newStatus = ($card->status === 'active') ? 'blocked' : 'active';
        $card->update([
            'status' => $newStatus,
            'blocked_at' => ($newStatus === 'blocked') ? Carbon::now() : null,
        ]);

        $statusLabel = ($newStatus === 'active') ? 'unblocked & active' : 'temporarily frozen/blocked';
        return back()->with('success', "Card {$card->card_number_masked} is now {$statusLabel}.");
    }

    public function chequeBookRequest(Request $request)
    {
        $user = Auth::user();
        $profile = $user->customerProfile;
        $accounts = $profile->accounts()->where('status', 'active')->get();

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'bank_account_id' => 'required|exists:bank_accounts,id',
                'leaves' => 'required|in:25,50,100',
            ]);

            $acc = BankAccount::findOrFail($validated['bank_account_id']);
            $bookNum = 'CHQ-' . $acc->branch->code . '-' . rand(1000, 9999);
            $start = rand(10000000, 90000000);
            $end = $start + (int)$validated['leaves'] - 1;

            $book = ChequeBook::create([
                'bank_account_id' => $acc->id,
                'book_number' => $bookNum,
                'start_leaf' => (string)$start,
                'end_leaf' => (string)$end,
                'total_leaves' => (int)$validated['leaves'],
                'status' => 'active',
                'issued_at' => Carbon::now(),
            ]);

            for ($i = 0; $i < 5; $i++) {
                ChequeLeaf::create([
                    'cheque_book_id' => $book->id,
                    'bank_account_id' => $acc->id,
                    'leaf_number' => (string)($start + $i),
                    'status' => 'unused',
                ]);
            }

            return redirect()->route('portal.cheque_books')->with('success', "Cheque Book request #{$bookNum} processed! Sent for MICR printing.");
        }

        $chequeBooks = ChequeBook::whereIn('bank_account_id', $accounts->pluck('id'))->with('leaves')->latest()->get();
        return view('portal.cheque_books', compact('accounts', 'chequeBooks'));
    }

    public function loanSimulator()
    {
        return view('portal.loan_simulator');
    }
}
