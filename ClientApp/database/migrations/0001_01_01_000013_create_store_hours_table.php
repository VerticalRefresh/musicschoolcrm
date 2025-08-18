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
            $table->id();
            $table->timestampsTz();
            $table->unsignedTinyInteger('weekday');
            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();
            $table->text('notes')->nullable();
            $table->unique(['franchise_id', 'weekday']);
            $table->boolean('is_closed')->default(false);
            $table->foreignId('franchise_id')->constrained()->cascadeOnDelete();

        });
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
