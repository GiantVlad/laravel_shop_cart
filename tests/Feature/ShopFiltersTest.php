<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Catalog;
use App\Product;
use App\Property;
use App\PropertyValue;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ShopFiltersTest extends TestCase
{
    public function testShopPageExposesCategoryTreeAndFilterProperties(): void
    {
        $root = Catalog::factory()->create(['name' => 'Tools', 'parent_id' => null, 'priority' => 1]);
        $child = Catalog::factory()->create(['name' => 'Hammers', 'parent_id' => $root->id, 'priority' => 1]);
        $property = Property::factory()->create([
            'name' => 'Color',
            'type' => Property::TYPE_SELECTOR,
            'priority' => 1,
        ]);
        PropertyValue::factory()->create(['property_id' => $property->id, 'value' => 'black']);

        $response = $this->get('/shop');

        $response->assertSuccessful();
        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('ProductList', false)
                ->has('categories', 1)
                ->where('categories.0.id', $root->id)
                ->has('categories.0.children', 1)
                ->where('categories.0.children.0.id', $child->id)
                ->has('properties', 1)
                ->where('properties.0.id', $property->id)
                ->has('properties.0.property_values', 1)
        );
    }

    public function testShopPageFiltersProductsByCategorySubtree(): void
    {
        $root = Catalog::factory()->create(['name' => 'Tools', 'parent_id' => null, 'priority' => 1]);
        $child = Catalog::factory()->create(['name' => 'Hammers', 'parent_id' => $root->id, 'priority' => 1]);
        $other = Catalog::factory()->create(['name' => 'Toys', 'parent_id' => null, 'priority' => 2]);

        $matching = Product::factory()->create(['catalog_id' => $child->id]);
        Product::factory()->create(['catalog_id' => $other->id]);

        $response = $this->get('/shop?category_id=' . $root->id);

        $response->assertSuccessful();
        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('ProductList', false)
                ->has('products.data', 1)
                ->where('products.data.0.id', $matching->id)
        );
    }

    public function testShopPageFiltersProductsByPropertyValue(): void
    {
        $property = Property::factory()->create([
            'name' => 'Color',
            'type' => Property::TYPE_SELECTOR,
            'priority' => 1,
        ]);
        $black = PropertyValue::factory()->create(['property_id' => $property->id, 'value' => 'black']);
        $white = PropertyValue::factory()->create(['property_id' => $property->id, 'value' => 'white']);

        $blackProduct = Product::factory()->create();
        $blackProduct->properties()->attach($black->id);

        $whiteProduct = Product::factory()->create();
        $whiteProduct->properties()->attach($white->id);

        $response = $this->get('/shop?values_' . $property->id . '=' . $black->id);

        $response->assertSuccessful();
        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('ProductList', false)
                ->has('products.data', 1)
                ->where('products.data.0.id', $blackProduct->id)
        );
    }
}
