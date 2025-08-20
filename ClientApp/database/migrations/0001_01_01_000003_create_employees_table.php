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
        Schema::create('employees', function (Blueprint $table) {
            $table->id(); //PK
            $table->timestampsTz(); //Zone sensitive timestamps

            $table->string('first_name'); //Names
            $table->string('last_name');

            $table->string('email')->nullable(); //Contact information
            $table->string('phone', 32)->nullable();

            $table->string('title')->nullable(); //Position with franchise/company

            $table->foreignId('franchise_id')->nullable(); //If assigned to a franchise, nullable w/ add_foreign migration

            $table->index(['last_name', 'first_name']); //For rapid search and get

            //Address from address polymorphic table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            Schema::dropIfExists('employees');
        });
    }
};
