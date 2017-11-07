<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CatalogsFirstChildTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $parent_id = \App\Catalog::where('name', 'Appliances')->first()->id;
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

        $parent_id = \App\Catalog::where('name', 'Furniture')->first()->id;

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

        $parent_id = \App\Catalog::where('name', 'Food')->first()->id;

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
    }
}
