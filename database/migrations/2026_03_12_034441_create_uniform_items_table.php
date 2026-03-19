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
        Schema::create('uniform_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uniform_category_id')->constrained();
            $table->string('uniform_item_name')->unique();
            $table->text('uniform_item_description')->nullable();
            $table->decimal('uniform_item_price', 15, 2);
            $table->string('uniform_item_image')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uniform_items');
    }
};
