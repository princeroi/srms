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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('contact_person');
            $table->string('email');
            $table->string('contact_number');
            $table->string('address')->nullable();
            $table->date('contract_start_date');
            $table->date('contract_renewal_date');
            $table->date('contract_end_date')->nullable();
            $table->enum('status', [
                'active',
                'inactive',
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
