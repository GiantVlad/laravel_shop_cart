<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Link payment methods to the shipping methods that offer them.
     */
    public function up(): void
    {
        Schema::create('payment_method_shipping_method', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('shipping_method_id');
            $table->unsignedInteger('payment_method_id');
            $table->timestamps();

            $table->unique(['shipping_method_id', 'payment_method_id'], 'shipping_payment_unique');
            $table->foreign('shipping_method_id')->references('id')->on('shipping_methods')->cascadeOnDelete();
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->cascadeOnDelete();
        });

        $now = now()->format('Y-m-d H:i:s');
        $shippingMethodIds = DB::table('shipping_methods')->pluck('id');
        $paymentMethodIds = DB::table('payment_methods')->pluck('id');

        $rows = [];
        foreach ($shippingMethodIds as $shippingMethodId) {
            foreach ($paymentMethodIds as $paymentMethodId) {
                $rows[] = [
                    'shipping_method_id' => $shippingMethodId,
                    'payment_method_id' => $paymentMethodId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // The shipping methods that exist keep exactly the payment options they had
        // before this relationship existed; only new methods get new combinations.
        if ($rows !== []) {
            DB::table('payment_method_shipping_method')->insert($rows);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_method_shipping_method');
    }
};
