<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Loans, Islamic Murabaha & Consumer Financing Engine.
     */
    public function up(): void
    {
        Schema::create('loans_financing', function (Blueprint $table) {
            $table->id();
            $table->string('loan_reference')->unique(); // e.g. LNF-2026-0041
            $table->foreignId('customer_profile_id')->constrained('customer_profiles')->cascadeOnDelete();
            $table->foreignId('bank_account_id')->constrained('bank_accounts')->cascadeOnDelete(); // Disbursed to & repayment from
            $table->string('product_type'); // personal_finance, auto_loan, home_murabaha, solar_financing
            $table->decimal('principal_amount', 18, 2);
            $table->decimal('markup_rate', 5, 2); // e.g. 17.50% (KIBOR + spread)
            $table->integer('tenor_months'); // e.g. 12, 24, 36, 60
            $table->decimal('monthly_installment', 14, 2);
            $table->decimal('total_payable', 18, 2);
            $table->decimal('remaining_balance', 18, 2);
            $table->integer('installments_paid')->default(0);
            $table->string('status')->default('active'); // active, disbursed, defaulted, closed
            $table->date('disbursed_date')->nullable();
            $table->date('maturity_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans_financing');
    }
};
