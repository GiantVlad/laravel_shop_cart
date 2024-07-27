<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTableChangeTypeColumnCartNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function(Blueprint $table) {
            $table->text('cart')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function(Blueprint $table) {
            $table->text('cart')->change();
        });
    }
}
