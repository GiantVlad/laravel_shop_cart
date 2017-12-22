<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ShippingMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run ()
    {
        DB::table('shipping_methods')->truncate();
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
        ]);
    }
}
