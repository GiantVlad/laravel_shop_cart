<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Order;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'order_label' => 'prfx_' . $this->faker->unique()->ean8(),
            'commentary' => $this->faker->text(),
            'status' => $this->faker->randomElement(['pending payment', 'process', 'completed', 'deleted']),
            'total' => $this->faker->randomFloat(2, 0.05, 999999),
            'user_id' => fn () => User::factory()->create()->id,
        ];
    }
}
