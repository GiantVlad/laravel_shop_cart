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
        Catalog::factory()->count(5)->create();
        $this->catalog = new Catalog;
    }

    /**
     * Get catalogs tree test.
     *
     * @return void
     */
    public function test_get_catalog_ids_tree ()
    {
        $this->catalog->whereIn('id', [3,4])->update(['parent_id' => 1]);

        $catalog_ids = $this->catalog->getCatalogIdsTree(1);

        $passed = true;
        if (($catalog_ids[0] !== 1) ||
            ($catalog_ids[1] !== 3) ||
            ($catalog_ids[2] !== 4)) $passed = false;

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
