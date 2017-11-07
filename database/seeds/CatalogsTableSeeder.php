<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CatalogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('catalogs')->insert([
            [
                'name' => 'Appliances',
                'image' => 'appliances_cat.jpg',
                'description' => 'description for Appliances',
                'parent_id' => NULL,
                'priority' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'Furniture',
                'image' => 'furniture_cat.jpg',
                'description' => 'description for Furniture',
                'parent_id' => NULL,
                'priority' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'Food',
                'image' => 'food_cat.jpg',
                'description' => 'description for Food',
                'parent_id' => NULL,
                'priority' => 2,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'Jewelry',
                'image' => 'jewelry_cat.jpg',
                'description' => 'description for Jewelry',
                'parent_id' => NULL,
                'priority' => 3,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
        ]);
    }
}
