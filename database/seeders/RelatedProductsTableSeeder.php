<?php

namespace Database\Seeders;

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class RelatedProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $related_products = [];
        for ($i = 1; $i < 201; $i++) {

            $related_products[] =
                [
                    'points' => 1000,
                    'impressions' => 0,
                    'choices' => 0,
                    'deleting' => 0,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ];

        }
        DB::table('related_products')->truncate();
        DB::table('related_products')->insert($related_products);
    }
}


