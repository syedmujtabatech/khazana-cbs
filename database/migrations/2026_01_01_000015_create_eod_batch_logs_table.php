<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * EOD (End of Day) & BOD (Beginning of Day) Batch Run Audit.
     * Records interest accrual runs, TDR auto-renewals, GL balance consolidation, and vault balance roll-overs.
     */
    public function up(): void
    {
        Schema::create('eod_batch_logs', function (Blueprint $table) {
            $table->id();
            $table->string('batch_reference')->unique(); // e.g. EOD-20261031-901
            $table->date('business_date');
            $table->foreignId('triggered_by')->constrained('users')->cascadeOnDelete();
            $table->integer('accounts_processed')->default(0);
            $table->decimal('total_profit_accrued', 18, 2)->default(0.00);
            $table->decimal('tdr_matured_amount', 18, 2)->default(0.00);
            $table->boolean('gl_balanced')->default(true);
            $table->decimal('vault_discrepancy', 14, 2)->default(0.00);
            $table->string('status')->default('completed'); // in_progress, completed, failed
            $table->json('execution_summary')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eod_batch_logs');
    }
};
