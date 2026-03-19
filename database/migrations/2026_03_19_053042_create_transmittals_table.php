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
        Schema::create('transmittals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uniform_issuance_id')->constrained()->cascadeOnDelete();
            $table->string('transmittal_number')->unique();
            $table->string('transmitted_by');
            $table->string('transmitted_to');
            $table->json('items_summary'); 
            $table->string('purpose')->nullable();
            $table->string('instructions')->nullable();
            $table->date('transmitted_at');
            $table->enum('status', ['pending', 'received'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transmittals');
    }
};
