<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStatusOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['pending payment', 'process', 'completed', 'deleted'])->default('pending payment');
            $table->string('order_label')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['process', 'completed', 'deleted'])->default('process');
            $table->dropColumn('order_label');
        });
    }
}
