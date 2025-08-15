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
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->timestampsTz();
            $table->foreignId('franchise_id') //Checked independently of hours table, takes precedence in code
            ->constrained('franchises')
            ->cascadeOnDelete();
            $table->string('name');  //Holiday name, from ENUM for sake of holiday date refactoring (easter, labor day, etc)
            $table->boolean('closed')->notNullValue()->default(false);
            $table->text('notes')->nullable();  //Amended hours, etc
            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();
            $table->date('date');
            $table->unique(['franchise_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
