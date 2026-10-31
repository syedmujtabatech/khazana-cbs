<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankAccount;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\CustomerProfile;
use App\Models\Transaction;
use App\Models\MakerCheckerRequest;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. High-level CBS Metrics
        $totalDeposits = BankAccount::where('status', 'active')->sum('available_balance');
        $totalVaultCash = Branch::sum('current_vault_cash');
        $totalCustomers = CustomerProfile::count();
        $totalAccounts = BankAccount::count();

        // 2. Today's Activity
        $todayVolume = Transaction::whereDate('created_at', Carbon::today())
            ->where('status', 'posted')
            ->sum('amount');
        $todayCount = Transaction::whereDate('created_at', Carbon::today())->count();

        // 3. Operational Queue
        $pendingMakerChecker = MakerCheckerRequest::where('status', 'pending')->count();
        $amlAlertsCount = Transaction::where('is_aml_flagged', true)->count();
        $ctrReportedCount = Transaction::where('ctr_reported', true)->count();

        // 4. Branch Breakdown
        $branches = Branch::withCount('accounts')->get();

        // 5. Recent Transaction Feed
        $recentTransactions = Transaction::with(['sourceAccount.customer', 'destinationAccount.customer', 'branch', 'maker'])
            ->latest()
            ->take(8)
            ->get();

        // 6. Recent Pending Maker-Checker items
        $recentMakerRequests = MakerCheckerRequest::with(['maker', 'branch'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('cbs.dashboard', compact(
            'totalDeposits',
            'totalVaultCash',
            'totalCustomers',
            'totalAccounts',
            'todayVolume',
            'todayCount',
            'pendingMakerChecker',
            'amlAlertsCount',
            'ctrReportedCount',
            'branches',
            'recentTransactions',
            'recentMakerRequests'
        ));
    }
}
