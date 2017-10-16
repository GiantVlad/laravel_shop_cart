<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class RelatedProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('related_products')->insert([
            [
                'product_id' => 1,
                'related_product_id' => 2,
                'points' => 1000,
                'impressions' => 0,
                'choices' => 0,
                'deleting' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'product_id' => 1,
                'related_product_id' => 3,
                'points' => 1000,
                'impressions' => 0,
                'choices' => 0,
                'deleting' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'product_id' => 1,
                'related_product_id' => 4,
                'points' => 1000,
                'impressions' => 0,
                'choices' => 0,
                'deleting' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'product_id' => 1,
                'related_product_id' => 5,
                'points' => 1000,
                'impressions' => 0,
                'choices' => 0,
                'deleting' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'product_id' => 1,
                'related_product_id' => 6,
                'points' => 1000,
                'impressions' => 0,
                'choices' => 0,
                'deleting' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'product_id' => 2,
                'related_product_id' => 8,
                'points' => 1000,
                'impressions' => 0,
                'choices' => 0,
                'deleting' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'product_id' => 2,
                'related_product_id' => 9,
                'points' => 1000,
                'impressions' => 0,
                'choices' => 0,
                'deleting' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'product_id' => 2,
                'related_product_id' => 4,
                'points' => 1000,
                'impressions' => 0,
                'choices' => 0,
                'deleting' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
