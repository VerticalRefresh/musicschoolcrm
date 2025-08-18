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
            $table->id();
            $table->timestampsTz();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone', 32);
            $table->foreignId('franchise_id')
            ->nullable()
            ->constrained('franchises')
            ->nullOnDelete();
            $table->index(['last_name', 'first_name']);
            $table->decimal('balance', 10, 2)->default(0);
            $table->unsignedTinyInteger('certification'); //1-5 stars
            $table->string('age_group');
        });
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
