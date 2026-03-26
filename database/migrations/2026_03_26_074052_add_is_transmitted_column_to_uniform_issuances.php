<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uniform_issuances', function (Blueprint $table) {
            $table->boolean('is_transmitted')->default(false)->after('signed_receiving_copy');
        });
    }

    public function down(): void
    {
        Schema::table('uniform_issuances', function (Blueprint $table) {
            $table->dropColumn('is_transmitted');
        });
    }
};