<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Product::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'catalog_id' => random_int(1, 10),
        'description' => $faker->paragraph,
        'price' => $faker->randomFloat(2, 0.01, 9999999.99),
        'image' => 'product_img.jpg'
    ];
});

