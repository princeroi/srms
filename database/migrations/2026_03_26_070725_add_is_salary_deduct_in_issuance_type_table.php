<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uniform_issuance_types', function (Blueprint $table) {
            $table->boolean('is_salary_deduct')->default(false)->after('uniform_issuance_type_name');
        });

        DB::table('uniform_issuance_types')
            ->where('uniform_issuance_type_name', 'Salary Deduct')
            ->update(['is_salary_deduct' => true]);
    }

    public function down(): void
    {
        Schema::table('uniform_issuance_types', function (Blueprint $table) {
            $table->dropColumn('is_salary_deduct');
        });
    }
};