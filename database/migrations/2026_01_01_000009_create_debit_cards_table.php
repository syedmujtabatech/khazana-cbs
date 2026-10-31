<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Debit Cards Engine (PayPak / Visa / Mastercard chip & contactless).
     */
    public function up(): void
    {
        Schema::create('debit_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained('bank_accounts')->cascadeOnDelete();
            $table->foreignId('customer_profile_id')->constrained('customer_profiles')->cascadeOnDelete();
            $table->string('card_number_masked'); // e.g. 5241 89** **** 4892
            $table->string('card_token')->unique(); // Tokenized identifier
            $table->string('card_type')->default('paypak'); // paypak, visa_debit, mastercard_world
            $table->string('embossed_name');
            $table->string('expiry_month', 2);
            $table->string('expiry_year', 4);
            $table->string('cvv_hash');
            $table->string('pin_hash');
            $table->string('status')->default('active'); // active, blocked, inactive, hotlisted
            $table->boolean('is_ecommerce_enabled')->default(true);
            $table->boolean('is_international_enabled')->default(false);
            $table->boolean('is_contactless_enabled')->default(true);
            $table->decimal('daily_atm_limit', 14, 2)->default(50000.00);
            $table->decimal('daily_pos_limit', 14, 2)->default(100000.00);
            $table->decimal('daily_ecom_limit', 14, 2)->default(50000.00);
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('blocked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debit_cards');
    }
};
