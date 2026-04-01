<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::create('billing_drs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uniform_issuance_id')->constrained()->onDelete('cascade');
            $table->foreignId('uniform_issuance_billing_id')->constrained()->onDelete('cascade');
            $table->string('employee_name');
            $table->string('dr_number');
            $table->date('date_signed')->nullable();
            $table->string('dr_image')->nullable();       // stored file path
            $table->string('remarks')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_drs');
    }
};
