<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Dispatch;
use Illuminate\Database\Eloquent\Factories\Factory;

class DispatchFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Dispatch::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'shipping_method_id' => $this->faker->unique()->numberBetween(1, 9),
            'order_id' => $this->faker->numberBetween(1, 99999),
            'code' => $this->faker->numberBetween(1, 99),
            'details' => $this->faker->text(),
        ];
    }
}
