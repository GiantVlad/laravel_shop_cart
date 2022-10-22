<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Catalog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class CatalogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = Catalog::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(15, true),
            'description' => $this->faker->sentence,
            'parent_id' => null,
        ];
    }
}
