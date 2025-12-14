<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            // Ensure password column is long enough
            $table->string('password', 255)->change();

            // Add missing default fields if they don't exist
            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken()->nullable();
            }

            if (!Schema::hasColumn('users', 'created_at') || !Schema::hasColumn('users', 'updated_at')) {
                $table->timestamps();
            }
        });
    }

    public function down()
    {
        // Optional: rollback logic
    }
};
