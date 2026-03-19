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
        Schema::create('uniform_restocks', function (Blueprint $table) {
            $table->id();
             $table->string('supplier_name');
            $table->string('ordered_by');
            $table->date('ordered_at');
            $table->enum('status', [
                'pending',
                'delivered',
            ]);
            $table->date('pending_at')->nullable();
            $table->date('delivered_at')->nullable();
            $table->date('partial_at')->nullable();
            $table->date('cancelled_at')->nullable();
            $table->text('notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uniform_restocks');
    }
};
