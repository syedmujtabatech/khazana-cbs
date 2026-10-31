<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Maker-Checker Workflow Table (Four-Eyes Principle).
     * Mandatory for high-value transactions (> 100k), account freezes, and GL adjustments.
     */
    public function up(): void
    {
        Schema::create('maker_checker_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique(); // e.g. MKR-20261031-8932
            $table->string('action_type'); // transaction_approval, account_freeze, credit_limit_change, gl_adjustment
            $table->foreignId('maker_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('checker_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->decimal('amount', 18, 2)->default(0.00);
            $table->string('currency', 3)->default('PKR');
            $table->json('payload'); // All transaction or operation details in JSON
            $table->string('status')->default('pending'); // pending, approved, rejected, cancelled
            $table->text('maker_notes')->nullable();
            $table->text('checker_notes')->nullable();
            $table->timestamp('actioned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maker_checker_requests');
    }
};
