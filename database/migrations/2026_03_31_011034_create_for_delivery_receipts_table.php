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
        Schema::create('for_delivery_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uniform_issuance_id')->constrained()->onDelete('cascade');
            $table->string('endorse_by');
            $table->date('endorse_date')->nullable();
            $table->json('item_summary');
            $table->enum('status', ['pending', 'done', 'cancelled'])->default('pending');
            $table->date('done_date')->nullable();
            $table->date('cancel_date')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('for_delivery_receipts');
    }
};
