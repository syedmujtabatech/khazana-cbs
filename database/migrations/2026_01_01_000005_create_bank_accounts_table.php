<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_number', 16)->unique(); // 14-digit local account number e.g. 0010100201
            $table->string('iban', 34)->unique(); // SBP 24-char IBAN e.g. PK36KHZN0010100201
            $table->foreignId('customer_profile_id')->constrained('customer_profiles')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('account_product_id')->constrained('account_products')->cascadeOnDelete();
            $table->string('account_title');
            $table->string('currency', 3)->default('PKR');
            $table->decimal('available_balance', 18, 2)->default(0.00);
            $table->decimal('ledger_balance', 18, 2)->default(0.00);
            $table->decimal('hold_amount', 18, 2)->default(0.00);
            $table->decimal('accrued_profit', 18, 2)->default(0.00);
            $table->decimal('tdr_principal', 18, 2)->nullable();
            $table->date('tdr_maturity_date')->nullable();
            $table->boolean('is_tdr_auto_rollover')->default(true);
            $table->string('status')->default('active'); // active, dormant, frozen, closed
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('frozen_at')->nullable();
            $table->timestamp('dormant_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
