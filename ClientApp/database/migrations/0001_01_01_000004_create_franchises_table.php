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
            $table->id();
            $table->timestampsTz();
            $table->foreignId('owner_id')
            ->constrained('employees')
            ->restrictOnDelete();
            $table->index('owner_id');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->foreignId('emergency_contact_id')
            ->nullable()
            ->constrained('employees')
            ->nullOnDelete();
            $table->index('emergency_contact_id');
            $table->string('timezone', 64)->default('America/New_York');
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
