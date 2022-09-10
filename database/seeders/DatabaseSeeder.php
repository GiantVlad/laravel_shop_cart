<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run ()
    {
        $this->call(
            [
                CatalogsTableSeeder::class,
                UnitsTableSeeder::class,
                ProductsTableSeeder::class,
                RelatedProductsTableSeeder::class,
                ProductsRelatedProductsTableSeeder::class,
                PropertiesTableSeeder::class,
                PropertyValuesTableSeeder::class,
                ProductPropertiesTableSeeder::class,
                AdminsTableSeeder::class,
                ShippingMethodsTableSeeder::class,
            ]
        );
    }
}
