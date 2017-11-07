<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CatalogsSecondChildTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run ()
    {
        $parent_id = \App\Catalog::where('name', 'TVs')->first()->id;
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

