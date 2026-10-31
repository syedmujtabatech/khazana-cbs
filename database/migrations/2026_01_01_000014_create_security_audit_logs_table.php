<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * SBP Mandatory Forensic Security Audit Trail.
     * Records all user actions, IP addresses, tamper detection, and sensitive operations.
     */
    public function up(): void
    {
        Schema::create('security_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type'); // auth_login, maker_checker_action, account_freeze, transaction_posted, aml_flagged
            $table->string('severity')->default('info'); // info, notice, warning, critical
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->text('description');
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_audit_logs');
    }
};
