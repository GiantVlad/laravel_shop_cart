<?php

declare(strict_types=1);

namespace Database\Factories;

use App\ShippingMethod;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShippingMethodFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ShippingMethod::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws Exception
     */
    public function definition()
    {
        return [
            'class_name' => $this->faker->word,
            'priority' => $this->faker->numberBetween(1, 50),
            'enable' => $this->faker->boolean,
        ];
    }
}
