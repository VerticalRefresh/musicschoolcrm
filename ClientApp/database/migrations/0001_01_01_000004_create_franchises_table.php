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
        Schema::create('franchises', function (Blueprint $table) {
            $table->id(); //PK
            $table->timestampsTz(); //Zone sensitive timestamps

            $table->foreignId('owner_id') //Franchise owner, for emergency contact, etc.
            ->constrained('employees')
            ->restrictOnDelete();
            $table->index('owner_id');

            $table->string('phone'); //Store contact information
            $table->string('email')->nullable();

            $table->foreignId('emergency_contact_id') //Emergency contact information for optional second employee
            ->nullable()
            ->constrained('employees')
            ->nullOnDelete();
            $table->index('emergency_contact_id');

            $table->string('timezone', 64)->default('America/New_York'); //Set timezone for conversions

            //Address from address polymorphic table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('franchises');
    }
};
