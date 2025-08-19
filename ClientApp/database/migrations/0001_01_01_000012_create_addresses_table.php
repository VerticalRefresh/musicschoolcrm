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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id(); //PK
            $table->timestampsTz(); //Zone sensitive timestamps

            $table->morphs('addressable'); //Employees, Franchises, Guardians, Tutors, Students
            $table->unique(['addressable_type', 'addressable_id']);

            //Data prepared for possible international expansion
            $table->string('line1', 255);
            $table->string('line2', 255)->nullable();
            $table->string('city', 100);
            $table->string('region', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->index(['postal_code']);
            $table->string('country_code', 2)->default('US');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
