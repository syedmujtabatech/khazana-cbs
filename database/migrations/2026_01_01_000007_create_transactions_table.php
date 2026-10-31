<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_reference', 40)->unique();
            $table->foreignId('source_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->foreignId('destination_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('maker_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('checker_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('transaction_type'); // cash_deposit, cash_withdrawal, internal_transfer, 1link_ibft, raast_instant, bill_payment
            $table->string('channel')->default('teller'); // teller, digital, atm, pos
            $table->string('currency', 3)->default('PKR');
            $table->decimal('amount', 18, 2);
            $table->decimal('fee', 14, 2)->default(0.00);
            $table->decimal('tax', 14, 2)->default(0.00);
            $table->decimal('net_amount', 18, 2);
            $table->text('narration')->nullable();
            
            $table->string('external_bank_name')->nullable();
            $table->string('external_iban')->nullable();
            $table->string('external_beneficiary_name')->nullable();
            $table->string('cheque_number')->nullable();
            
            $table->string('status')->default('posted'); // posted, pending_approval, rejected, reversed
            $table->boolean('is_aml_flagged')->default(false);
            $table->text('aml_reason')->nullable();
            $table->boolean('ctr_reported')->default(false); // SBP CTR threshold report
            
            $table->timestamp('posted_at')->nullable();
            $table->timestamp('authorized_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
