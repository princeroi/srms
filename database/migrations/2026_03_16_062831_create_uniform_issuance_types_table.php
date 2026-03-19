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
        Schema::create('uniform_issuance_types', function (Blueprint $table) {
            $table->id();
            $table->string('uniform_issuance_type_name');
            $table->timestamps();
        });

        DB::table('uniform_issuance_types')->insert([
            [
                'uniform_issuance_type_name' => 'New Hire',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uniform_issuance_type_name' => 'Additional',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uniform_issuance_type_name' => 'Salary Deduct',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uniform_issuance_type_name' => 'Annual',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uniform_issuance_types');
    }
};
