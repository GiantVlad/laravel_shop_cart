<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
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
                PropertiesTableSeeder::class,
                PropertyValuesTableSeeder::class,
                ProductPropertiesTableSeeder::class,
                AdminsTableSeeder::class
            ]
        );
    }
}
