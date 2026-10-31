<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EodBatchLog;
use App\Models\BankAccount;
use App\Services\EodBatchService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EodBatchController extends Controller
{
    protected EodBatchService $eodService;

    public function __construct(EodBatchService $eodService)
    {
        $this->eodService = $eodService;
    }

    public function index()
    {
        $batchLogs = EodBatchLog::with('triggerUser')->latest()->paginate(10);
        $lastRun = EodBatchLog::latest()->first();
        $savingsCount = BankAccount::whereHas('product', fn($q) => $q->where('category', 'savings'))->count();
        $activeAccountsCount = BankAccount::where('status', 'active')->count();

        return view('cbs.eod.index', compact('batchLogs', 'lastRun', 'savingsCount', 'activeAccountsCount'));
    }

    public function runBatch(Request $request)
    {
        $user = Auth::user();

        if (!$user->isChecker()) {
            return back()->with('error', 'Only Branch Managers or Super Admins are authorized to execute End-of-Day Batch.');
        }

        try {
            $batch = $this->eodService->runEodBatch($user, $request->business_date);
            return redirect()->route('cbs.eod.index')->with('success', "EOD Batch {$batch->batch_reference} executed successfully! Accounts Processed: {$batch->accounts_processed}, Profit Accrued: PKR " . number_format($batch->total_profit_accrued, 2));
        } catch (\Exception $e) {
            return back()->with('error', 'Batch Execution Failed: ' . $e->getMessage());
        }
    }
}
