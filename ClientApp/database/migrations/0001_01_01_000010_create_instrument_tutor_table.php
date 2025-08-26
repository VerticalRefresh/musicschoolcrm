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
        Schema::create('instrument_tutor', function (Blueprint $table) {
            $table->timestampsTz(); //Zone sensitive timestamps

            //Table takes from instrument/tutor to create combined table, add additional information
            $table->foreignId('instrument_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tutor_id')->constrained()->cascadeOnDelete();

            $table->string('proficiency', 20)->nullable(); //ENUM in code

            $table->unsignedSmallInteger('years')->default(0); //With instrument

            $table->boolean('is_primary')->default('false'); //For multidisciplinary tutors

            $table->primary(['instrument_id', 'tutor_id']);
        });

        //Sanity constraints
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'pgsql') {
            \Illuminate\Support\Facades\DB::statement(
                "ALTER TABLE instrument_tutor ADD CONSTRAINT instrument_years_nonneg CHECK (years >= 0)"
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instrument_tutor');
    }
};
