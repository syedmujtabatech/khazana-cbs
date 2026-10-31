<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chart of Accounts (COA) - Double-Entry General Ledger Structure.
     */
    public function up(): void
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_code', 20)->unique(); // e.g. 1010, 2010, 4010
            $table->string('account_name');
            $table->enum('account_type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->string('sub_category')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->string('currency', 3)->default('PKR');
            $table->decimal('current_balance', 18, 2)->default(0.00);
            $table->string('normal_balance')->default('debit'); // debit or credit
            $table->text('description')->nullable();
            $table->boolean('is_reconciled')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
