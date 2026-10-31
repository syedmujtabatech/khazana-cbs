<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Beneficiaries table for Customer Internet Banking & Mobile Banking.
     */
    public function up(): void
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nick_name');
            $table->string('beneficiary_name');
            $table->string('transfer_type'); // internal, 1link_ibft, raast_p2p
            $table->string('bank_name')->default('Khazana Bank');
            $table->string('bank_code')->nullable(); // SBP BIC or 1LINK IMCE code
            $table->string('account_or_iban'); // 14-digit account or 24-char IBAN or Raast Phone
            $table->string('raast_id')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
