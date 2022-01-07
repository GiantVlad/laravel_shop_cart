<?php

declare(strict_types=1);

namespace Database\Factories;

use App\PropertyValue;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyValueFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PropertyValue::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws Exception
     */
    public function definition()
    {
        return [
            'property_id' => $this->faker->numberBetween(1, 100),
            'value' => $this->faker->text(30),
            'unit_id' => $this->faker->numberBetween(1, 20),
        ];
    }
}
