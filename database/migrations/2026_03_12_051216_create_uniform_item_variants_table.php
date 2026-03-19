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
        Schema::create('uniform_item_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uniform_item_id')->constrained();
            $table->string('uniform_item_size');
            $table->integer('uniform_item_quantity');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['uniform_item_id', 'uniform_item_size']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uniform_item_variants');
    }
};
