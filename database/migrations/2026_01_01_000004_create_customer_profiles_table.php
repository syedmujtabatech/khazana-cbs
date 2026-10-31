<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('cif_number', 20)->unique(); // e.g. CIF-0010-00101
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('salutation', 15)->default('Mr.');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('father_or_husband_name')->nullable();
            $table->string('mother_maiden_name')->nullable();
            $table->string('cnic_passport', 30)->unique(); // 42101-1234567-1
            $table->date('cnic_expiry_date')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->default('male');
            $table->string('nationality')->default('Pakistani');
            $table->string('occupation')->nullable();
            $table->string('source_of_income')->nullable();
            $table->decimal('monthly_income', 14, 2)->nullable();
            $table->text('residential_address');
            $table->string('city');
            $table->string('country')->default('Pakistan');
            $table->boolean('is_pep')->default(false); // Politically Exposed Person
            $table->string('risk_rating')->default('low'); // low, medium, high, pep
            $table->string('kyc_status')->default('verified'); // verified, pending, rejected
            $table->boolean('biometric_verified')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_profiles');
    }
};
