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
        Schema::create('uniform_issuance_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uniform_issuance_recipient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uniform_item_id')->constrained();
            $table->foreignId('uniform_item_variant_id')->constrained();
            $table->integer('quantity');
            $table->integer('released_quantity');
            $table->integer('remaining_quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uniform_issuance_items');
    }
};

