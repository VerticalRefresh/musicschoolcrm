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
            //Holidays table will be checked independently of hours table and take precedence in code.
            $table->id(); //PK
            $table->timestampsTz(); //Zone sensitive Timestamps

            $table->foreignId('franchise_id') //Removed per holiday
            ->constrained('franchises')
            ->cascadeOnDelete();

            $table->string('name');  //Holiday name, from ENUM for sake of holiday date refactoring (easter, labor day, etc)

            $table->boolean('closed')->notNullValue()->default(false);

            $table->text('notes')->nullable();  //Amended hours, etc

            $table->time('opens_at')->nullable(); //For open holiday hours, nullable if closed
            $table->time('closes_at')->nullable();

            $table->date('date'); //For searching, plus yearly refactoring for date ambiguous (Easter, etc) can be coded later.
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
