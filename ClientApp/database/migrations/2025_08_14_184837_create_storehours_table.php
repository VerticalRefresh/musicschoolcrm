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
        Schema::create('storehours', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->smallInteger('weekday');
            $table->time('opens_at');
            $table->time('closes_at');
            $table->text('notes');
            $table->foreignId('franchise_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storehours');
    }
};
