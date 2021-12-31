<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Property;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Property::class;

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
            'prop_group_id' => random_int(1, 100),
            'priority' => $this->faker->numberBetween(1, 50),
            'type' => $this->faker->randomElement(Property::getTypes()),
        ];
    }
}
