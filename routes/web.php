<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerMasterController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TellerController;
use App\Http\Controllers\MakerCheckerController;
use App\Http\Controllers\GeneralLedgerController;
use App\Http\Controllers\AmlComplianceController;
use App\Http\Controllers\EodBatchController;
use App\Http\Controllers\CustomerPortalController;

/*
|--------------------------------------------------------------------------
| Web Routes - Khazana Core Banking System (Khazana-CBS)
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isCustomer() 
            ? redirect()->route('portal.dashboard') 
            : redirect()->route('cbs.dashboard');
    }
    return redirect()->route('login');
});

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Fast Role / Profile Switcher (Evaluation Utility)
Route::get('/demo-switch/{role}', [AuthController::class, 'switchRole'])->name('demo.switch');

// CBS Staff & Admin Routes
Route::middleware(['auth'])->prefix('cbs')->name('cbs.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Customer Master (CIF)
    Route::get('/customers', [CustomerMasterController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerMasterController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerMasterController::class, 'store'])->name('customers.store');
    Route::get('/customers/{id}', [CustomerMasterController::class, 'show'])->name('customers.show');

    // Bank Accounts & SBP IBAN
    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::get('/accounts/create', [AccountController::class, 'create'])->name('accounts.create');
    Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');
    Route::get('/accounts/{id}', [AccountController::class, 'show'])->name('accounts.show');
    Route::post('/accounts/{id}/freeze', [AccountController::class, 'toggleFreeze'])->name('accounts.freeze');

    // Teller Cash Desk
    Route::get('/teller', [TellerController::class, 'index'])->name('teller.index');
    Route::get('/teller/deposit', [TellerController::class, 'depositForm'])->name('teller.deposit');
    Route::post('/teller/deposit', [TellerController::class, 'processDeposit'])->name('teller.deposit.process');
    Route::get('/teller/withdrawal', [TellerController::class, 'withdrawalForm'])->name('teller.withdrawal');
    Route::post('/teller/withdrawal', [TellerController::class, 'processWithdrawal'])->name('teller.withdrawal.process');

    // Maker-Checker Authorization Queue
    Route::get('/maker-checker', [MakerCheckerController::class, 'index'])->name('maker_checker.index');
    Route::get('/maker-checker/{id}', [MakerCheckerController::class, 'show'])->name('maker_checker.show');
    Route::post('/maker-checker/{id}/approve', [MakerCheckerController::class, 'approve'])->name('maker_checker.approve');
    Route::post('/maker-checker/{id}/reject', [MakerCheckerController::class, 'reject'])->name('maker_checker.reject');

    // General Ledger & Accounting
    Route::get('/general-ledger/chart-of-accounts', [GeneralLedgerController::class, 'chartOfAccounts'])->name('gl.coa');
    Route::get('/general-ledger/trial-balance', [GeneralLedgerController::class, 'trialBalance'])->name('gl.trial_balance');
    Route::get('/general-ledger/journal', [GeneralLedgerController::class, 'journalEntries'])->name('gl.journal');
    Route::post('/general-ledger/reversals/{id}', [GeneralLedgerController::class, 'reverseTransaction'])->name('gl.reverse');

    // SBP AML / CFT Compliance & Audit
    Route::get('/compliance', [AmlComplianceController::class, 'index'])->name('compliance.index');
    Route::get('/compliance/ctr-reports', [AmlComplianceController::class, 'ctrReports'])->name('compliance.ctr');
    Route::get('/compliance/pep-registry', [AmlComplianceController::class, 'pepList'])->name('compliance.pep');
    Route::get('/compliance/audit-trail', [AmlComplianceController::class, 'auditTrail'])->name('compliance.audit');

    // End of Day (EOD) Batch
    Route::get('/eod-batch', [EodBatchController::class, 'index'])->name('eod.index');
    Route::post('/eod-batch/run', [EodBatchController::class, 'runBatch'])->name('eod.run');
});

// Customer Digital Internet Banking Portal Routes
Route::middleware(['auth'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/dashboard', [CustomerPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/statement', [CustomerPortalController::class, 'statement'])->name('statement');
    
    // Funds Transfer (Internal, 1LINK IBFT, Raast)
    Route::get('/transfer', [CustomerPortalController::class, 'transferForm'])->name('transfer');
    Route::post('/transfer', [CustomerPortalController::class, 'processTransfer'])->name('transfer.process');

    // 1BILL Utility Payments
    Route::get('/bill-payment', [CustomerPortalController::class, 'billPaymentForm'])->name('bill_payment');
    Route::post('/bill-payment', [CustomerPortalController::class, 'processBillPayment'])->name('bill_payment.process');

    // Debit Cards Self-Service
    Route::get('/cards', [CustomerPortalController::class, 'cardControls'])->name('cards');
    Route::post('/cards/{id}/update', [CustomerPortalController::class, 'updateCard'])->name('cards.update');
    Route::post('/cards/{id}/toggle-block', [CustomerPortalController::class, 'toggleCardBlock'])->name('cards.toggle_block');

    // Cheque Books
    Route::get('/cheque-books', [CustomerPortalController::class, 'chequeBookRequest'])->name('cheque_books');
    Route::post('/cheque-books', [CustomerPortalController::class, 'chequeBookRequest'])->name('cheque_books.request');

    // Consumer Loan & Murabaha Simulator
    Route::get('/loan-simulator', [CustomerPortalController::class, 'loanSimulator'])->name('loan_simulator');
});
