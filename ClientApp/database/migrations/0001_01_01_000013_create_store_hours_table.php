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
        Schema::create('store_hours', function (Blueprint $table) {
            $table->id();  //PK
            $table->timestampsTz(); //Zone sensitive timestamps

            $table->unsignedTinyInteger('weekday'); //0-6 Sun-Sat

            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();

            $table->text('notes')->nullable(); //For lunch closures, other information

            $table->unique(['franchise_id', 'weekday']); //Indexes and ensures one entry for each day of the week per franchise

            $table->boolean('is_closed')->default(false);

            $table->foreignId('franchise_id')->constrained()->cascadeOnDelete(); //Removes store hours on delete of franchise

        });

        //Sanity constraints
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'pgsql') {
            \Illuminate\Support\Facades\DB::statement(
                "ALTER TABLE store_hours ADD CONSTRAINT weekday_value_binding CHECK (weekday >= 0 AND weekday <= 6)"
            );
            \Illuminate\Support\Facades\DB::statement(
                "ALTER TABLE store_hours ADD CONSTRAINT opens_before_closes CHECK (opens_at < closes_at)"
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storehours');
    }
};
