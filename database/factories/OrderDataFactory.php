<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Order;
use App\OrderData;
use App\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderDataFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderData::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'order_id' => fn () => (int)Order::factory()->create()->id,
            'product_id' => fn () => (int)Product::factory()->create()->id,
            'is_related_product' => $this->faker->boolean(),
            'price' => $this->faker->randomFloat(2, 0.05, 999999),
            'qty' => $this->faker->numberBetween(1, 100),
        ];
    }
}
