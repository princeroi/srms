<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transmittals', function (Blueprint $table) {
            $table->string('received_from_office')->nullable()->after('status');
            $table->date('date_received_from_office')->nullable()->after('received_from_office');
            $table->string('received_from_site')->nullable()->after('date_received_from_office');
            $table->date('date_received_from_site')->nullable()->after('received_from_site');
            $table->string('remarks')->nullable()->after('date_received_from_site');
        });
    }

    public function down(): void
    {
        Schema::table('transmittals', function (Blueprint $table) {
            $table->dropColumn([
                'received_from_office',
                'date_received_from_office',
                'received_from_site',
                'date_received_from_site',
                'remarks',
            ]);
        });
    }
};