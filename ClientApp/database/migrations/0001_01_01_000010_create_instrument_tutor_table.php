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
            $table->id();
            $table->timestampsTz();
            $table->foreignId('instrument_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tutor_id')->constrained()->cascadeOnDelete();
            $table->string('proficiency', 20)->nullable();
            $table->unsignedSmallInteger('years')->default(0);
            $table->boolean('is_primary')->default('false');

            $table->primary(['instrument_id', 'tutor_id']);
        });
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
