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
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->foreignId('client_id')->constrained();
            $table->text('notes')->nullable();
            $table->date('billing_start_period');
            $table->date('billing_end_period');
            $table->date('billing_date');
            $table->date('due_date');
            $table->decimal('total_amount', 15, 2);
            $table->enum('status', [
                'pending',
                'partially_paid',
                'paid',
                'overdue',
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
