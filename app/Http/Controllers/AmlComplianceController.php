<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\CustomerProfile;
use App\Models\SecurityAuditLog;
use Carbon\Carbon;

class AmlComplianceController extends Controller
{
    public function index()
    {
        $flaggedTransactions = Transaction::where('is_aml_flagged', true)
            ->with(['sourceAccount.customer', 'destinationAccount.customer', 'branch'])
            ->latest()
            ->take(10)
            ->get();

        $ctrCount = Transaction::where('ctr_reported', true)->count();
        $totalFlagged = Transaction::where('is_aml_flagged', true)->count();
        $pepCount = CustomerProfile::where('is_pep', true)->count();

        $recentAudits = SecurityAuditLog::with('user')->latest()->take(10)->get();

        return view('cbs.compliance.index', compact(
            'flaggedTransactions',
            'ctrCount',
            'totalFlagged',
            'pepCount',
            'recentAudits'
        ));
    }

    public function ctrReports()
    {
        $ctrTransactions = Transaction::where('ctr_reported', true)
            ->with(['sourceAccount.customer', 'destinationAccount.customer', 'branch', 'maker'])
            ->latest()
            ->paginate(15);

        return view('cbs.compliance.ctr_reports', compact('ctrTransactions'));
    }

    public function pepList()
    {
        $pepCustomers = CustomerProfile::where(function($q) {
            $q->where('is_pep', true)
              ->orWhere('risk_rating', 'pep')
              ->orWhere('risk_rating', 'high');
        })->with(['user', 'accounts'])->paginate(15);

        return view('cbs.compliance.pep_list', compact('pepCustomers'));
    }

    public function auditTrail(Request $request)
    {
        $query = SecurityAuditLog::with('user');

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('description', 'like', "%{$s}%")
                  ->orWhere('event_type', 'like', "%{$s}%")
                  ->orWhere('ip_address', 'like', "%{$s}%");
            });
        }

        $logs = $query->latest()->paginate(25);
        return view('cbs.compliance.audit_trail', compact('logs'));
    }
}
