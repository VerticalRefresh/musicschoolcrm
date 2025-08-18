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
        Schema::create('instrument_student', function (Blueprint $table) {
            $table->id();
            $table->timestampsTz();
            $table->foreignId('instrument_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('level', 20)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->date('started_on')->nullable();

            $table->primary(['instrument_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instrument_student', function (Blueprint $table) {
            Schema::dropIfExists('instrument_student');
        });
    }
};
