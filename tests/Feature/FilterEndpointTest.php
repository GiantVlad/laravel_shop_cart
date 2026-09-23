<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Product;
use App\Property;
use App\PropertyValue;
use Tests\TestCase;

class FilterEndpointTest extends TestCase
{
    public function testItReturnsProductsMatchingASelectorValue(): void
    {
        $color = Property::factory()->create(['name' => 'color', 'type' => Property::TYPE_SELECTOR]);
        $black = PropertyValue::factory()->create(['property_id' => $color->id, 'value' => 'black']);

        $matching = Product::factory()->create();
        $matching->properties()->attach($black->id);
        Product::factory()->create();

        $response = $this->get('/filter?values_' . $color->id . '=' . $black->id);

        $response->assertSuccessful();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id', $matching->id);
    }

    public function testItIgnoresNonFilterQueryParams(): void
    {
        Product::factory()->create();

        $this->get('/filter?page=2&sort=name')->assertSuccessful();
    }
}
