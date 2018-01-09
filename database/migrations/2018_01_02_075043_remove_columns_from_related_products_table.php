<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveColumnsFromRelatedProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('related_products', function (Blueprint $table) {
            $table->dropColumn('product_id', 'related_product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('related_products', function (Blueprint $table) {
            $table->integer('product_id')->default(0);
            $table->integer('related_product_id')->default(0);
        });
    }
}
