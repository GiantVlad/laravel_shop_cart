<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'payment_method_id' => $this->faker->unique()->numberBetween(1, 9),
            'order_id' => $this->faker->numberBetween(1, 99999),
            'status' => $this->faker->randomElement([Payment::STATUS_CREATED, Payment::STATUS_INITIALIZED, Payment::STATUS_PAID]),
            'external_id' => $this->faker->uuid(),
            'details' => $this->faker->text(),
        ];
    }
}
