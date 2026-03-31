<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('uniform_issuance_billings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uniform_issuance_id')->constrained('uniform_issuances')->cascadeOnDelete();
            $table->string('billed_to');
            $table->enum('billing_type', ['client', 'salary_deduct', 'other'])->default('client');
            $table->json('billing_items');         // [{item_name, size, quantity, unit_price, employee?}]
            $table->decimal('total_price', 12, 2)->default(0);
            $table->enum('status', ['pending', 'billed'])->default('pending');
            $table->date('billed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uniform_issuance_billings_tables');
    }
};
