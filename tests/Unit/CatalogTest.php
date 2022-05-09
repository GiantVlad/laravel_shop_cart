<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Catalog;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    private $catalog;

    public function setUp (): void
    {
        parent::setUp();
        $this->catalogs = Catalog::factory()->count(5)->create();
        $this->catalog = new Catalog;
    }

    /**
     * Get catalogs tree test.
     *
     * @return void
     */
    public function test_get_catalog_ids_tree()
    {
        $first = $this->catalogs->first()->id;
        $third = $this->catalogs[2]->id;
        $fourth = $this->catalogs[3]->id;
        $this->catalog->whereIn('id', [$third, $fourth])->update(['parent_id' => $first]);

        $catalog_ids = $this->catalog->getCatalogIdsTree($first);

        $passed = $catalog_ids[0] === $first && $catalog_ids[1] === $third && $catalog_ids[2] === $fourth;

        $this->assertTrue($passed);
    }

    /**
     * Get catalogs without parents test.
     *
     * @return void
     */
    public function testParentsNode ()
    {
        $catalogs = $this->catalog->parentsNode();
        $isParentNull = true;
        foreach ($catalogs as $catalog) {
            if (!is_null($catalog->parent_id)) {
                $isParentNull = false;
            }
        }
        $this->assertTrue($isParentNull);
    }
}
