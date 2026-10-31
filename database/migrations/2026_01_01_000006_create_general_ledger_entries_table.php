<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->string('batch_reference', 40)->index();
            $table->integer('entry_number')->default(1);
            $table->foreignId('chart_of_account_id')->constrained('chart_of_accounts')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->unsignedBigInteger('transaction_id')->nullable()->index();
            $table->date('value_date');
            $table->string('entry_type'); // debit or credit
            $table->decimal('amount', 18, 2);
            $table->string('currency', 3)->default('PKR');
            $table->string('narration');
            $table->boolean('is_reversal')->default(false);
            $table->unsignedBigInteger('reversal_for_entry_id')->nullable()->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_ledger_entries');
    }
};
