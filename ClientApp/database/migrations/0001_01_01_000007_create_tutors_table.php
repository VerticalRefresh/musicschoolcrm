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
        Schema::create('tutors', function (Blueprint $table) {
            $table->id(); //PK
            $table->timestampsTz(); //Zone sensitive timestamps

            $table->string('first_name'); //Name fields
            $table->string('last_name');

            $table->string('email'); //Contact info
            $table->string('phone', 32);

            $table->foreignId('franchise_id') //Primary franchise for tutor, not constraining tutor to specific franchise.
            ->nullable()
            ->constrained('franchises')
            ->nullOnDelete();

            $table->index(['last_name', 'first_name']); //Rapid search and get

            $table->decimal('balance', 10, 2)->default(0); //For payroll, per student session

            $table->unsignedTinyInteger('certification'); //1-5 stars, for certification program

            $table->string('age_group'); //To assist with enrollment

            //Addresses from polymorphic table
        });

        //Add data constraints for safety
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'pgsql') {
            \Illuminate\Support\Facades\DB::statement(
                "ALTER TABLE tutors ADD CONSTRAINT tutors_balance_nonneg CHECK (balance >= 0)"
            );
            \Illuminate\Support\Facades\DB::statement(
                "ALTER TABLE tutors ADD CONSTRAINT tutors_certification_range CHECK (certification BETWEEN 1 AND 5)"
            );
        }
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutors');
    }
};
