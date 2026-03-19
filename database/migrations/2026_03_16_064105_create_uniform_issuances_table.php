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
        Schema::create('uniform_issuances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained();
            $table->foreignId('uniform_issuance_type_id')->constrained();
            $table->enum('uniform_issuance_status', [
                'pending','issued'
            ]);
            $table->date('pending_at')->nullable();
            $table->date('partial_at')->nullable();
            $table->date('issued_at')->nullable();
            $table->date('cancelled_at')->nullable();
            $table->string('signed_receiving_copy')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uniform_issuances');
    }
};
