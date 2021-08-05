<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\RelatedProduct;
use App\Services\Recommended\Recommended;
use Illuminate\Database\Eloquent\Builder;
use PHPUnit\Framework\TestCase;

class RecommendedTest extends TestCase
{
    private RelatedProduct $relatedProduct;
    
    public function setUp (): void
    {
        parent::setUp();
        $this->relatedProduct = $this->createMock(RelatedProduct::class);
    }
    /**
     * @return void
     */
    public function testIncrementRate()
    {
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())->method('increment')->with('points', -3);
        
        $this->relatedProduct->expects($this->once())
            ->method('__call')
            ->with('where', ['id', 123])
            ->willReturn($builder);
        
        $recommended = new Recommended($this->relatedProduct);
        $recommended->incrementRate(123, -3);
    }
}
