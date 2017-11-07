<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Faker\Factory as Faker;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $productsArray = [];
        for ($i=1; $i< 200; $i++) {
            $productsArray[] = [
                'name' => $faker->realText(rand(10,15)),
                'image' => 'prod'.$faker->numberBetween(1,6).'.jpg',
                'description' => $faker->paragraph(2, true),
                'price' => $faker->numberBetween(100,100000)/100,
                'catalog_id' => $faker->numberBetween(1,16),
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ];
        }
        DB::table('products')->truncate();
        DB::table('products')->insert($productsArray);
    }
}
