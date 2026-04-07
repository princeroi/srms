<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->string('billing_title')->nullable()->after('id');
            $table->string('invoice_number')->nullable()->change();
            $table->decimal('total_amount', 15, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->dropColumn('billing_title');
            $table->string('invoice_number')->nullable(false)->change();
            $table->decimal('total_amount', 15, 2)->nullable(false)->change();
        });
    }
};