<?php

namespace Tests\Feature\Repositories;

use App\DTO\FilterNumberDTO;
use App\DTO\FilterSelectorDTO;
use App\Product;
use App\Property;
use App\PropertyValue;
use App\Repositories\ProductRepository;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Factories\Sequence;

class ProductRepositoryTest extends TestCase
{
    public function testGetFilteredProducts()
    {
        $color = Property::factory()->create(['name' => 'color', 'type' => Property::TYPE_SELECTOR]);
        $length = Property::factory()->create(['name' => 'length', 'type' => Property::TYPE_NUMBER]);
        
        $product = Product::factory()
            ->has(
                PropertyValue::factory()
                    ->count(2)
                    ->state(
                        new Sequence(
                            ['value' => '20.56', 'property_id' => $length->id],
                            ['value' => 'black', 'property_id' => $color->id],
                        )
                    ),
                'properties'
            )
            ->create();
    
        Product::factory()
            ->has(PropertyValue::factory()->state(['value' => 'blue', 'property_id' => $color->id]), 'properties')
            ->create();
        Product::factory()
            ->has(PropertyValue::factory()->state(new Sequence(
                ['value' => '20.44', 'property_id' => $length->id],
                ['value' => 'white', 'property_id' => $color->id]
            )), 'properties')
            ->create();
        
        $repository = new ProductRepository(new Product());
        $filters = new Collection();
        $filters->add(new FilterNumberDTO(0, 20.56, $length->id));
        $filters->add(new FilterSelectorDTO([$product->properties()->get()->last()->id], $color->id));
        $result = $repository->getFilteredProducts($filters);
        $this->assertCount(1, $result);
        $this->assertEquals($product->id, $result->first()->id);
    }
}
