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
        Schema::create('billing_atds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uniform_issuance_id')->constrained()->onDelete('cascade');
            $table->foreignId('uniform_issuance_billing_id')->constrained()->onDelete('cascade');
            $table->string('employee_name');
            $table->date('date_signed')->nullable();
            $table->string('atd_image')->nullable();      // stored file path
            $table->string('remarks')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_atds');
    }
};
