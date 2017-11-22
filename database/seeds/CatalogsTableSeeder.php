<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Catalog;

class CatalogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('catalogs')->truncate();
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
        $parent_id = Catalog::where('name', 'Appliances')->first()->id;
        DB::table('catalogs')->insert([
            [
                'name' => 'TVs',
                'image' => 'tv_cat.jpg',
                'description' => 'description for TV',
                'parent_id' => $parent_id,
                'priority' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'Refrigerators',
                'image' => 'refrigerators_cat.jpg',
                'description' => 'description for Refrigerators',
                'parent_id' => $parent_id,
                'priority' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'Cellphones',
                'image' => 'cellphones_cat.jpg',
                'description' => 'description for Cellphones',
                'parent_id' => $parent_id,
                'priority' => 2,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
        ]);

        $parent_id = Catalog::where('name', 'Furniture')->first()->id;

        DB::table('catalogs')->insert([
            [
                'name' => 'Sofas',
                'image' => 'sofas_cat.jpg',
                'description' => 'description for Sofas',
                'parent_id' => $parent_id,
                'priority' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'Cupboards',
                'image' => 'cupboards_cat.jpg',
                'description' => 'description for Cupboards',
                'parent_id' => $parent_id,
                'priority' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'Beds',
                'image' => 'beds_cat.jpg',
                'description' => 'description for Beds',
                'parent_id' => $parent_id,
                'priority' => 2,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
        ]);

        $parent_id = Catalog::where('name', 'Food')->first()->id;

        DB::table('catalogs')->insert([
            [
                'name' => 'Milk',
                'image' => 'milk_cat.jpg',
                'description' => 'description for Milk',
                'parent_id' => $parent_id,
                'priority' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bread',
                'image' => 'bread_cat.jpg',
                'description' => 'description for Bread',
                'parent_id' => $parent_id,
                'priority' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'Sweets',
                'image' => 'sweets_cat.jpg',
                'description' => 'description for Sweets',
                'parent_id' => $parent_id,
                'priority' => 2,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
        ]);
        $parent_id = Catalog::where('name', 'TVs')->first()->id;
        DB::table('catalogs')->insert([
            [
                'name' => 'LCD',
                'image' => 'lcd_cat.jpg',
                'description' => 'description for LCD TVs',
                'parent_id' => $parent_id,
                'priority' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'Plasma',
                'image' => 'plasma_cat.jpg',
                'description' => 'description for Plasma TVs',
                'parent_id' => $parent_id,
                'priority' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'LED',
                'image' => 'led_tv_cat.jpg',
                'description' => 'description for LED TVs',
                'parent_id' => $parent_id,
                'priority' => 2,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
        ]);
    }
}
