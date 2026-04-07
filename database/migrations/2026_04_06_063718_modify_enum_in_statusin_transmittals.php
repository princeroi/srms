<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix the enum
        DB::statement("ALTER TABLE transmittals MODIFY COLUMN status ENUM('pending', 'received_from_office', 'received_from_site', 'document_returned') NOT NULL DEFAULT 'pending'");

        Schema::table('transmittals', function (Blueprint $table) {
            // Return document fields
            $table->string('returned_by')->nullable()->after('remarks');
            $table->date('date_returned')->nullable()->after('returned_by');
        });
    }

    public function down(): void
    {
        Schema::table('transmittals', function (Blueprint $table) {
            $table->dropColumn(['returned_by', 'date_returned']);
        });

        DB::statement("ALTER TABLE transmittals MODIFY COLUMN status ENUM('pending', 'received') NOT NULL DEFAULT 'pending'");
    }
};