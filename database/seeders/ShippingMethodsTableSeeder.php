<?php

namespace Database\Seeders;

use App\PaymentMethod;
use App\ShippingMethod;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ShippingMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run ()
    {
        // delete() instead of truncate(): payment_method_shipping_method has a
        // foreign key on this table, and MariaDB refuses to truncate a referenced table.
        DB::table('shipping_methods')->delete();
        DB::table('shipping_methods')->insert([
            [
                'class_name' => 'FreeShippingMethod',
                'priority' => 1,
                'enable' => true,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'class_name' => 'FixRateShippingMethod',
                'priority' => 0,
                'enable' => true,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'class_name' => 'PickupShippingMethod',
                'priority' => 2,
                'enable' => true,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
        ]);

        $paymentMethods = PaymentMethod::orderBy('priority')->get();
        // Delivery shipping methods keep the online payment options; cash is
        // pickup-only, so store pickup takes every payment method.
        $online = $paymentMethods->where('config_key', '!=', 'cash')->pluck('id')->all();
        $everything = $paymentMethods->pluck('id')->all();

        foreach (ShippingMethod::orderBy('priority')->get() as $shippingMethod) {
            /** @var ShippingMethod $shippingMethod */
            $shippingMethod->paymentMethods()->sync(
                $shippingMethod->class_name === 'PickupShippingMethod' ? $everything : $online
            );
        }
    }
}
