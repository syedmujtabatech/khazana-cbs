<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MakerCheckerRequest;
use App\Services\MakerCheckerService;
use Illuminate\Support\Facades\Auth;

class MakerCheckerController extends Controller
{
    protected MakerCheckerService $makerCheckerService;

    public function __construct(MakerCheckerService $makerCheckerService)
    {
        $this->makerCheckerService = $makerCheckerService;
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $requests = MakerCheckerRequest::with(['maker', 'checker', 'branch'])
            ->where('status', $status)
            ->latest()
            ->paginate(15);

        $pendingCount = MakerCheckerRequest::where('status', 'pending')->count();
        $approvedCount = MakerCheckerRequest::where('status', 'approved')->count();
        $rejectedCount = MakerCheckerRequest::where('status', 'rejected')->count();

        return view('cbs.maker_checker.index', compact('requests', 'status', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }

    public function show($id)
    {
        $request = MakerCheckerRequest::with(['maker', 'checker', 'branch'])->findOrFail($id);
        return view('cbs.maker_checker.show', compact('request'));
    }

    public function approve(Request $request, $id)
    {
        $makerRequest = MakerCheckerRequest::findOrFail($id);
        $user = Auth::user();

        try {
            $result = $this->makerCheckerService->approveRequest(
                $makerRequest,
                $user,
                $request->input('checker_notes', 'Approved upon verification.')
            );
            return redirect()->route('cbs.maker_checker.index')->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $makerRequest = MakerCheckerRequest::findOrFail($id);
        $user = Auth::user();

        try {
            $this->makerCheckerService->rejectRequest($makerRequest, $user, $validated['reason']);
            return redirect()->route('cbs.maker_checker.index')->with('warning', "Request {$makerRequest->reference_no} rejected.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
