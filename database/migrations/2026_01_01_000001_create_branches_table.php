<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // e.g. 0010, 0020
            $table->string('name');
            $table->string('city');
            $table->string('province')->nullable();
            $table->text('address');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('swift_bic', 11)->default('KHZNPKKA');
            $table->string('routing_number', 15)->nullable();
            $table->boolean('is_head_office')->default(false);
            $table->decimal('vault_cash_limit', 18, 2)->default(50000000.00); // PKR 50M
            $table->decimal('current_vault_cash', 18, 2)->default(12500000.00); // PKR 12.5M
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
