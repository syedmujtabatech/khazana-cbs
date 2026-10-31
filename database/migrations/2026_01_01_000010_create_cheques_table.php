<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cheque Books and Individual Cheque Leaves Clearing Engine.
     */
    public function up(): void
    {
        Schema::create('cheque_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained('bank_accounts')->cascadeOnDelete();
            $table->string('book_number')->unique();
            $table->string('start_leaf');
            $table->string('end_leaf');
            $table->integer('total_leaves')->default(25);
            $table->string('status')->default('active'); // active, exhausted, stopped
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
        });

        Schema::create('cheque_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cheque_book_id')->constrained('cheque_books')->cascadeOnDelete();
            $table->foreignId('bank_account_id')->constrained('bank_accounts')->cascadeOnDelete();
            $table->string('leaf_number')->index();
            $table->decimal('amount', 18, 2)->nullable();
            $table->string('payee_name')->nullable();
            $table->string('status')->default('unused'); // unused, presented, cleared, bounced, stopped
            $table->string('bounce_reason')->nullable();
            $table->timestamp('cleared_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cheque_leaves');
        Schema::dropIfExists('cheque_books');
    }
};
