<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ProductPropertiesTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run ()
    {
        DB::table('product_property')->truncate();
        $faker = Faker::create();
        $data_array = [];
        for ($i = 1; $i < 100; $i++) {
            $data_array[] = [
                'property_value_id' => $faker->numberBetween(1, 12),
                'product_id' => $i,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ];
        }
        DB::table('product_property')->insert($data_array);
    }
}
