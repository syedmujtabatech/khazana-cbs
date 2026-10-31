<?php

namespace App\Services;

use App\Models\MakerCheckerRequest;
use App\Models\User;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;

class MakerCheckerService
{
    protected BankingLedgerService $ledgerService;
    protected AmlComplianceService $amlService;

    public function __construct(BankingLedgerService $ledgerService, AmlComplianceService $amlService)
    {
        $this->ledgerService = $ledgerService;
        $this->amlService = $amlService;
    }

    /**
     * Submit an action for Maker-Checker Authorization
     */
    public function submitRequest(
        User $maker,
        string $actionType,
        float $amount,
        array $payload,
        ?string $notes = null,
        ?int $branchId = null
    ): MakerCheckerRequest {
        $reference = 'MKR-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        return MakerCheckerRequest::create([
            'reference_no' => $reference,
            'action_type' => $actionType,
            'maker_id' => $maker->id,
            'branch_id' => $branchId ?? $maker->branch_id,
            'amount' => $amount,
            'currency' => $payload['currency'] ?? 'PKR',
            'payload' => $payload,
            'status' => 'pending',
            'maker_notes' => $notes,
        ]);
    }

    /**
     * Checker Authorizes the request
     */
    public function approveRequest(MakerCheckerRequest $request, User $checker, ?string $checkerNotes = null): array
    {
        // Check 1: Maker cannot approve their own request (Four-Eyes Principle)
        if ($request->maker_id === $checker->id) {
            throw new Exception("Segregation of Duties Violation: Maker cannot act as Checker on their own request.");
        }

        if ($request->status !== 'pending') {
            throw new Exception("Request is already in '{$request->status}' state.");
        }

        $payload = $request->payload;
        $result = null;

        // Execute corresponding CBS business operation
        switch ($request->action_type) {
            case 'cash_deposit':
                $account = BankAccount::findOrFail($payload['bank_account_id']);
                $maker = User::findOrFail($request->maker_id);
                $result = $this->ledgerService->postCashDeposit(
                    $account,
                    (float)$payload['amount'],
                    $maker,
                    $payload['narration'] ?? null,
                    $checker
                );
                break;

            case 'cash_withdrawal':
                $account = BankAccount::findOrFail($payload['bank_account_id']);
                $maker = User::findOrFail($request->maker_id);
                $result = $this->ledgerService->postCashWithdrawal(
                    $account,
                    (float)$payload['amount'],
                    $maker,
                    $payload['narration'] ?? null,
                    $checker
                );
                break;

            case 'internal_transfer':
                $source = BankAccount::findOrFail($payload['source_account_id']);
                $destination = BankAccount::findOrFail($payload['destination_account_id']);
                $maker = User::findOrFail($request->maker_id);
                $result = $this->ledgerService->postInternalTransfer(
                    $source,
                    $destination,
                    (float)$payload['amount'],
                    $maker,
                    $payload['narration'] ?? null,
                    $checker
                );
                break;

            case 'account_freeze':
                $account = BankAccount::findOrFail($payload['bank_account_id']);
                $account->update([
                    'status' => 'frozen',
                    'frozen_at' => Carbon::now(),
                ]);
                $result = $account;
                break;

            case 'account_unfreeze':
                $account = BankAccount::findOrFail($payload['bank_account_id']);
                $account->update([
                    'status' => 'active',
                    'frozen_at' => null,
                ]);
                $result = $account;
                break;

            default:
                throw new Exception("Unsupported action type: {$request->action_type}");
        }

        $request->update([
            'checker_id' => $checker->id,
            'status' => 'approved',
            'checker_notes' => $checkerNotes ?? 'Approved and verified by Checker.',
            'actioned_at' => Carbon::now(),
        ]);

        $this->amlService->logComplianceEvent(
            'maker_checker_approved',
            "Maker-Checker Request {$request->reference_no} approved by {$checker->name} for action {$request->action_type}.",
            $checker->id,
            ['request_id' => $request->id, 'amount' => $request->amount]
        );

        return [
            'success' => true,
            'message' => "Request {$request->reference_no} approved and executed successfully.",
            'result' => $result,
        ];
    }

    /**
     * Checker Rejects the request
     */
    public function rejectRequest(MakerCheckerRequest $request, User $checker, string $reason): void
    {
        if ($request->maker_id === $checker->id) {
            throw new Exception("Segregation of Duties Violation: Maker cannot reject their own request.");
        }

        if ($request->status !== 'pending') {
            throw new Exception("Request is already in '{$request->status}' state.");
        }

        $request->update([
            'checker_id' => $checker->id,
            'status' => 'rejected',
            'checker_notes' => $reason,
            'actioned_at' => Carbon::now(),
        ]);

        $this->amlService->logComplianceEvent(
            'maker_checker_rejected',
            "Maker-Checker Request {$request->reference_no} rejected by {$checker->name}. Reason: {$reason}",
            $checker->id,
            ['request_id' => $request->id]
        );
    }
}
