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
        Schema::create('students', function (Blueprint $table) {
            $table->id(); //PK
            $table->timestampsTz(); //Timestamps
            $table->string('first_name'); //Name fields
            $table->string('last_name');
            $table->string('email'); //Contact info
            $table->string('phone');
            $table->foreignId('tutor_id')->nullable() //One tutor per student, many students per tutor
            ->constrained('tutors')->nullOnDelete();
            $table->foreignId('franchise_id')->nullable() //One main franchise per student, many students per franchise
            ->constrained('franchises')->nullOnDelete();
            $table->decimal('subscription'); //Monthly payment for tutor(s)
            $table->decimal('balance'); //Balance owed
            $table->date('birthday'); //Birthday (for determining if guardian is needed, birthday perks)
            $table->foreignId('guardian_id')->nullable() //If has a guardian, referenced here
            ->constrained('guardians')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
