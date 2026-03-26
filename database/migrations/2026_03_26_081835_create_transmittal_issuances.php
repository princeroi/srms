<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transmittal_issuances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transmittal_id')
                ->constrained('transmittals')
                ->cascadeOnDelete();
            $table->foreignId('uniform_issuance_id')
                ->constrained('uniform_issuances')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transmittal_issuances');
    }
};