<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->insert([
            [
                'name' => 'General product',
                'image' => '123.jpg',
                'description' => 'description for general product',
                'price' => 125.14,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'General product 2',
                'image' => '123.jpg',
                'description' => 'description for general product 2',
                'price' => 78.25,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'Related product 1',
                'image' => 'rel1.jpg',
                'description' => 'description for related product',
                'price' => 15.45,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'Related product 2',
                'image' => 'rel2.jpg',
                'description' => 'description for related product',
                'price' => 9.99,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'Related product 3',
                'image' => 'rel3.jpg',
                'description' => 'description for related product',
                'price' => 28.17,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'Related product 4',
                'image' => 'rel4.jpg',
                'description' => 'description for related product',
                'price' => 55.12,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'Related product 5',
                'image' => 'rel5.jpg',
                'description' => 'description for related product',
                'price' => 4.32,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'Related product 6',
                'image' => 'rel4.jpg',
                'description' => 'description for related product 6',
                'price' => 24.18,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'Related product 7',
                'image' => 'rel4.jpg',
                'description' => 'description for related product 7',
                'price' => 37.39,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
