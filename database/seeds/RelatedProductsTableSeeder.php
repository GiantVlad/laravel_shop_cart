<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Faker\Factory as Faker;

class RelatedProductsTableSeeder extends Seeder
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
                do $related_product_id = $faker->numberBetween(1,200);
                while ($related_product_id === $i);
                $related_products[] =
                    [
                        'product_id' => $i,
                        'related_product_id' => $related_product_id,
                        'points' => 1000,
                        'impressions' => 0,
                        'choices' => 0,
                        'deleting' => 0,
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ];
            }
        }
        DB::table('related_products')->truncate();
        DB::table('related_products')->insert($related_products);
    }
}
