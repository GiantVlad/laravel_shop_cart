<?php

namespace Database\Factories;

use App\Product;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws Exception
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
            'catalog_id' => random_int(1, 10),
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 0.01, 9999999.99),
            'image' => 'product_img.jpg'
        ];
    }
}
