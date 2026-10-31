<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Billers (1BILL Utility Companies) & Bill Payments.
     */
    public function up(): void
    {
        Schema::create('billers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. KELECTRIC, SSGC, PTCL, FBR_TAX
            $table->string('name'); // e.g. K-Electric Limited
            $table->string('category'); // electricity, gas, telecom, government_tax, education
            $table->string('consumer_number_label')->default('Consumer Number / Bill Reference');
            $table->integer('consumer_number_length')->default(14);
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('bill_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biller_id')->constrained('billers')->cascadeOnDelete();
            $table->foreignId('bank_account_id')->constrained('bank_accounts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('consumer_number');
            $table->decimal('amount', 14, 2);
            $table->decimal('late_surcharge', 14, 2)->default(0.00);
            $table->date('due_date')->nullable();
            $table->string('billing_month')->nullable(); // e.g. Oct 2026
            $table->string('transaction_reference')->unique();
            $table->string('status')->default('paid'); // pending, paid, reversed
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bill_payments');
        Schema::dropIfExists('billers');
    }
};
