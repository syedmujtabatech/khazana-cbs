<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // CAA, PLS, TDR, FCY
            $table->string('name');
            $table->string('category'); // current, savings, tdr, foreign_currency
            $table->string('currency', 3)->default('PKR');
            $table->decimal('interest_rate', 5, 2)->default(0.00); // e.g. 11.50%
            $table->decimal('minimum_balance', 14, 2)->default(100.00);
            $table->decimal('daily_withdrawal_limit', 14, 2)->default(500000.00);
            $table->integer('tenor_months')->nullable(); // for TDR
            $table->decimal('early_withdrawal_penalty', 5, 2)->default(0.00);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_products');
    }
};
