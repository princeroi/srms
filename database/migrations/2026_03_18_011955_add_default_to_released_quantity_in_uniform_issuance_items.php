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
        Schema::table('uniform_issuance_items', function (Blueprint $table) {
            $table->integer('released_quantity')->default(0)->change();
            $table->integer('remaining_quantity')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uniform_issuance_items', function (Blueprint $table) {
            //
        });
    }
};
