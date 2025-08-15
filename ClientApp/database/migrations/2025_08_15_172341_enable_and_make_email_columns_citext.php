<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') return;

        DB::statement('CREATE EXTENSION IF NOT EXISTS citext;');  //make sure citext exists

        //All targets for email citext manipulation
        $targets = [
            ['table' => 'users',      'column' => 'email'],
            ['table' => 'students',   'column' => 'email'],
            ['table' => 'guardians',  'column' => 'email'],
            ['table' => 'tutors',     'column' => 'email'],
            ['table' => 'franchises', 'column' => 'email'],
        ];

        //Values from trusted list above
        foreach ($targets as $t) {
                $table  = $t['table'];
                $column = $t['column'];

                if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
                    DB::statement(sprintf(
                        'ALTER TABLE "%s" ALTER COLUMN "%s" TYPE CITEXT USING "%s"::citext',
                        $table, $column, $column
                    ));
                }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') return;

        //All targets for downgrade to varchar
        $targets = [
            ['table' => 'users',      'column' => 'email'],
            ['table' => 'students',   'column' => 'email'],
            ['table' => 'guardians',  'column' => 'email'],
            ['table' => 'tutors',     'column' => 'email'],
            ['table' => 'franchises', 'column' => 'email'],
        ];

        //Values from trusted list above
        foreach ($targets as $t) {
                $table  = $t['table'];
                $column = $t['column'];

                if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
                    DB::statement(sprintf(
                        'ALTER TABLE "%s" ALTER COLUMN "%s" TYPE VARCHAR(255)',
                        $table, $column
                    ));
                }
        }
    }
};
