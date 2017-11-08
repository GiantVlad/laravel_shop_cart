<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PropertiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('properties')->truncate();
        DB::table('properties')->insert([
            [
                'property_id' => 1,
                'name' => 'manufacturer',
                'value' => 'Canon',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'property_id' => 1,
                'name' => 'manufacturer',
                'value' => 'Philips',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'property_id' => 1,
                'name' => 'manufacturer',
                'value' => 'Bosch',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'property_id' => 1,
                'name' => 'manufacturer',
                'value' => 'Nesquik',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'property_id' => 1,
                'name' => 'manufacturer',
                'value' => 'Rolex',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'property_id' => 1,
                'name' => 'manufacturer',
                'value' => 'Ferrari',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
        ]);
    }
}
