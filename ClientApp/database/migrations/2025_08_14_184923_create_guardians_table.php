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
        Schema::create('guardians', function (Blueprint $table) {
            $table->id(); //Primary Key, ID
            $table->timestamps(); //Timestamps, audit logs
            $table->string('first_name'); //Name fields
            $table->string('last_name');
            $table->string('email'); //Contact information
            $table->string('phone');
            //Address from addresses polymorphic table
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
