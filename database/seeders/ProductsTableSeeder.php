<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    /**
     * Demo words for the generated names and descriptions.
     *
     * Plain PHP on purpose: fakerphp/faker is a require-dev package and the production image
     * runs `composer install --no-dev`, so Faker::create() killed `php artisan db:seed` there
     * with `Class "Faker\Factory" not found`.
     */
    private const WORDS = [
        'kettle', 'backpack', 'lamp', 'notebook', 'headphones', 'mug', 'chair', 'watch',
        'speaker', 'jacket', 'laptop', 'camera', 'blender', 'shelf', 'bottle', 'socks',
        'keyboard', 'monitor', 'hat', 'scarf',
    ];

    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $productsArray = [];
        for ($i=1; $i< 200; $i++) {
            $productsArray[] = [
                'name' => ucfirst($this->words(2)),
                'image' => 'prod'.mt_rand(1,6).'.jpg',
                'description' => ucfirst($this->words(6)).'. '.ucfirst($this->words(6)).'.',
                'price' => mt_rand(100,100000)/100,
                'catalog_id' => mt_rand(1,16),
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ];
        }
        DB::table('products')->truncate();
        DB::table('products')->insert($productsArray);
    }

    /**
     * A space separated phrase of $count words picked from the list above.
     */
    private function words(int $count): string
    {
        $words = [];
        for ($i = 0; $i < $count; $i++) {
            $words[] = self::WORDS[mt_rand(0, count(self::WORDS) - 1)];
        }

        return implode(' ', $words);
    }
}
