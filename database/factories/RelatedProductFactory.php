<?php

declare(strict_types=1);

namespace Database\Factories;

use App\RelatedProduct;
use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

class RelatedProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RelatedProduct::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'points' => $this->faker->numberBetween(0, 1000),
            'impressions' => $this->faker->numberBetween(0, 10000),
            'choices' => $this->faker->numberBetween(0, 10000),
            'deleting' => $this->faker->numberBetween(0, 10000),
        ];
    }
}
