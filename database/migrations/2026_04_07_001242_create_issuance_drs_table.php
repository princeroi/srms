<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issuance_dr', function (Blueprint $table) {
            $table->id();
            $table->foreignId('for_delivery_receipt_id')
                ->constrained('for_delivery_receipts')
                ->cascadeOnDelete();
            $table->string('dr_number');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issuance_dr');
    }
};