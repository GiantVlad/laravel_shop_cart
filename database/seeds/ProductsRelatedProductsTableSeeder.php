<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class ProductsRelatedProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $related_products = [];
        for ($i = 1; $i < 201; $i++) {
            for ($k = 1; $k < 6; $k++) {
                $related_product_id = $faker->numberBetween(1,200);
                if ($related_product_id != $i) {
                    $related_products[] =
                        [
                            'product_id' => $i,
                            'related_product_id' => $related_product_id,
                            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ];
                }
            }
        }
        DB::table('products_related_products')->truncate();
        DB::table('products_related_products')->insert($related_products);
    }
}
