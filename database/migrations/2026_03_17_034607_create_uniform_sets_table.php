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
        Schema::create('uniform_sets', function (Blueprint $table) {
            $table->id();
            $table->string('uniform_set_name');
            $table->text('uniform_set_description')->nullable();
            $table->foreignId('position_id')->constrained();
            $table->foreignId('site_id')->constrained();
            $table->enum('employee_status', [
                'all',
                'reliever',
                'posted',
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uniform_sets');
    }
};
