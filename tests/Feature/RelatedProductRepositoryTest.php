<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\RelatedProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelatedProductRepositoryTest extends TestCase
{
    use RefreshDatabase;
    
    public function testIncrementRate()
    {
        /** @var RelatedProduct $related */
        $related = RelatedProduct::factory()->create([
            'points' => 300,
        ]);
        RelatedProduct::where('id', $related->id)->increment('points', -5);
        $related->refresh();
        $this->assertEquals(295, $related->points);
    }
}
