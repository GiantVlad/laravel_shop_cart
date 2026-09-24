<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\DTO\FilterNumberDTO;
use App\DTO\FilterSelectorDTO;
use App\Product;
use App\Property;
use App\PropertyValue;
use App\Services\Filter\ProductFilterParser;
use Tests\TestCase;

class ProductFilterParserTest extends TestCase
{
    public function testItParsesSelectorAndNumberFilters(): void
    {
        $color = Property::factory()->create(['name' => 'color', 'type' => Property::TYPE_SELECTOR]);
        $weight = Property::factory()->create(['name' => 'weight', 'type' => Property::TYPE_NUMBER]);

        $filters = (new ProductFilterParser())->parse([
            'values_' . $color->id => '1,2',
            'values_' . $weight->id => '10,50',
        ]);

        $this->assertCount(2, $filters);

        $selector = $filters->first(fn ($dto) => $dto instanceof FilterSelectorDTO);
        $this->assertInstanceOf(FilterSelectorDTO::class, $selector);
        $this->assertSame([1, 2], $selector->getValues());
        $this->assertSame($color->id, $selector->getId());

        $number = $filters->first(fn ($dto) => $dto instanceof FilterNumberDTO);
        $this->assertInstanceOf(FilterNumberDTO::class, $number);
        $this->assertSame(10.0, $number->getMinValue());
        $this->assertSame(50.0, $number->getMaxValue());
        $this->assertSame($weight->id, $number->getId());
    }

    public function testItIgnoresNonFilterAndUnknownKeys(): void
    {
        Property::factory()->create(['name' => 'color', 'type' => Property::TYPE_SELECTOR]);

        $filters = (new ProductFilterParser())->parse([
            'page' => '2',
            'sort' => 'name',
            'values_999999' => '1',
            'values_0' => '1',
        ]);

        $this->assertCount(0, $filters);
    }
}
