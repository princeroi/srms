<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('for_delivery_receipts', function (Blueprint $table) {
            // Drop the existing foreign key constraint first
            $table->dropForeign(['uniform_issuance_id']);

            // Modify the column to be nullable
            $table->foreignId('uniform_issuance_id')
                ->nullable()
                ->change();

            // Re-add the foreign key constraint with nullOnDelete
            $table->foreign('uniform_issuance_id')
                ->references('id')
                ->on('uniform_issuances')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('for_delivery_receipts', function (Blueprint $table) {
            $table->dropForeign(['uniform_issuance_id']);

            $table->foreignId('uniform_issuance_id')
                ->nullable(false)
                ->change();

            $table->foreign('uniform_issuance_id')
                ->references('id')
                ->on('uniform_issuances')
                ->onDelete('cascade');
        });
    }
};