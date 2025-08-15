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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); //ID
            $table->string('email')->unique(); //Login email
            $table->timestampTz('email_verified_at')->nullable();  //Verified via link
            $table->string('password'); //Password hash, not plaintext
            $table->rememberToken();
            $table->timestampsTz(); //use Tz for conversion
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); //One token per email
            $table->string('token'); //Generated token
            $table->timestampTz('created_at')->nullable(); //Timestamp
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); //Session information
            $table->foreignId('user_id')
            ->constrained()
            ->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');

    }
};
