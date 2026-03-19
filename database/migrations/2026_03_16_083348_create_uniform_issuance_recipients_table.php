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
        Schema::create('uniform_issuance_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uniform_issuance_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_id')->unique();
            $table->string('employee_name');
            $table->foreignId('position_id')->constrained();
            $table->enum('employee_status', [
                'reliever',
                'posted',
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uniform_issuance_recipients');
    }
};



