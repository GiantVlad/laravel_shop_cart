# Plan: Restore the left filter/category column on `/shop`

## Goal

Bring back the left-hand sidebar on the shop page containing a **category list** and the **product property filters** (selector checkboxes + min/max number ranges), wired to real server-side filtering via Inertia, so that `/shop` again looks and behaves like the old `master` shop page.

---

## Current context / assumptions

**Stack (verified on disk, branch `dev2`, HEAD `270e728`)**

- Laravel 10 + Inertia v2 + Vue 3.5 + Vite 5 + PrimeVue 4 (Aura preset, sky-blue `#0284c7` primary).
- Frontend source: `frontend/src/`. Pages live in `frontend/src/Pages/`, resolved by `import.meta.glob('./Pages/**/*.vue')` in `frontend/src/main.js`.
- The app is served **from the built bundle** in `public/build/` — there is **no Vite dev server running** (nothing listening on `:5173`). `public/build/manifest.json` currently points at `assets/main-BY1rG0uz.js` / `assets/main-CqW190aj.css`.
- Site is live at `http://localhost/shop` (container `lara_shop-roadrunner-1`, host port 80, bind-mounts the repo at `/app`). `https://localhost/shop` does **not** work.
- `public/build/` is **committed to git** (see commit `e7fb587`), so built assets are part of the deliverable.

**What the old left column was** (confirmed in history)

`git show master:resources/views/app.blade.php` shows the old two-column shell:

```blade
<div class="col-xs-4 col-sm-3 col-lg-2">
    @yield('left-column')
</div>
<div class="col-xs-8 col-sm-9 col-lg-10 content">
    @yield('content')
</div>
```

and `git show master:resources/views/shop.blade.php` filled it with:

```blade
@section('categories')   <categories></categories>        @stop
@section('left-column')  <product-filters></product-filters> @stop
```

The Vue 2 components that powered it are recoverable from git:

- `git show master:resources/assets/js/components/ProductFilters.vue` — fetched `GET {baseUrl}/filter/properties`, rendered one `ProductFilter` per property, and on "Apply" emitted a global `product_filters` event.
- `git show master:resources/assets/js/components/ProductFilter.vue` — per-property control: checkbox list for `type === 'selector'`, min/max number inputs for `type === 'number'`.
- `git show master:resources/assets/js/components/Categories.vue` — fetched `GET {baseUrl}/shop/category/{id}`, rendered a breadcrumb + child-catalog links, emitted `category_changed`.

**Why it is gone**

1. Commit `4526b80` ("FE upgrade. Vue3 + Inertia") moved the old JS to `resources/assets/js_old/` (that directory no longer exists in the tree) and introduced `frontend/src/Pages/ProductList.vue`.
2. That new page **never** had a sidebar. In `4526b80`'s `ProductList.vue` every filter-related handler was commented out (`// this.$root.$on('product_filters', ...)`, `// this.$root.$on('category_changed', ...)`).
3. `1e303c1` ("refactoring /shop page") and `75602a7` (PrimeVue) rebuilt `ProductList.vue` into a single-column grid. The current file is 54 lines with no sidebar.
4. The Vue 2 implementation relied on `this.$root.$emit/$on`, which no longer exists in the Vue 3 app — so a **direct port is impossible**; the filters must be re-expressed in the Inertia way.

**Backend state (already exists, must be reused, not rewritten)**

| Thing | Location | Notes |
|---|---|---|
| `GET /shop` | `routes/web.php` → `ShopController@list` | Currently ignores **all** query params; returns `products` + `links` only |
| `GET /shop/category/{id}` | `ShopController@getChildCatalogs` | Returns `{"data": {"catalogs": [...], "parentCatalogs": [...]}}` — AJAX endpoint, not needed if we pass props |
| `GET /filter` | `SearchController@filter` | JSON endpoint, `ProductCollection`; parses `values_<propertyId>` params |
| `GET /filter/properties` | `FilterController@getFilterProperties` | JSON list of properties with values |
| `GET /shop/properties` | `routes/web.php:45` → `ShopController@getFilterProperties` | **DEAD ROUTE** — that method does not exist on `ShopController`; calling it 500s |
| `Catalog::getCatalogIdsTree(int $id): array` | `app/Catalog.php:29` | Walks the whole subtree — already the right primitive for category filtering |
| `ProductRepository::getFilteredProducts(Collection $filters, ?array $category_ids)` | `app/Repositories/ProductRepository.php:23` | Applies `FilterSelectorDTO` / `FilterNumberDTO`; **returns an unpaginated collection** |
| `PropertyRepository::getFilteredProducts()` | `app/Repositories/PropertyRepository.php` | Returns properties `with('propertyValues')` ordered by `priority` |
| `SearchController::prepareFilters()` | `app/Http/Controllers/SearchController.php:54` (private) | Converts `values_<id>` → DTOs. **Buggy**: any non-`values_*` key becomes `Property::findOrFail(0)` → 404 |

**Data model (verified against migrations)**

- `catalogs`: `id, name (unique), description, parent_id (nullable), image, priority (nullable), timestamps`.
- `products`: `id, name, description, price, image, catalog_id (int, default 0), timestamps`.
- `properties`: `id, name, prop_group_id, priority, type` where `type ∈ {'selector','number'}` (`App\Property::TYPE_SELECTOR` / `TYPE_NUMBER`).
- `property_values`: `id, property_id, value (string), unit_id (nullable)`.
- `product_property`: `id, product_id, property_value_id` (renamed from `property_id` by migration `2017_11_15_211208`).
- `Catalog` has **no** `children()` relation yet — must be added for the tree.
- `Property::propertyValues()` serialises to JSON key **`property_values`**; `PropertyValue.value` is the display label.

**Seed data** (`DatabaseSeeder`) — 4 root catalogs `Appliances, Furniture, Food, Jewelry` (each with children) and 2 properties: `manufacturer` (selector, priority 0), `weight` (number, priority 1).

> ⚠️ **The dev database is currently EMPTY**: `catalogs=0 roots=0 properties=0 values=0 products=0`. The sidebar will render nothing until you seed. See Pre-flight step P4.

**Test / tooling reality (verified by running them)**

- Tests **must** run inside the container; running `php artisan test` on the macOS host fails with `getaddrinfo for mariadb_test failed` (`.env.testing` sets `DB_HOST=mariadb_test`).
- Working command: `docker compose exec -T roadrunner php artisan test --filter=<Name>`
  Verified baseline: `php artisan test --filter=testGetShopPage` → `PASS Tests\Feature\HttpGetShopTest / ✓ get shop page / Tests: 1 passed (12 assertions)`.
- `docker-compose` (v1 binary) is **not installed**; use `docker compose` (v2). It resolves `compose.yaml`, which has **no bind mount** and uses `.env.prod`. Do **not** run `docker compose up -d` / rebuild — that would replace the currently running container (which is bind-mounted from `docker-compose.yml`) with a non-mounted one. `docker compose exec` is safe: it targets the already-running container by name.
- **PHPStan is broken repo-wide, pre-existing and unrelated to this work.** `./vendor/bin/phpstan analyse --memory-limit=2G --no-progress -c phpstan.neon ./` fails immediately with:
  ```
  Invalid configuration:
  Unexpected item 'parameters › checkMissingIterableValueType'.
  Unexpected item 'parameters › checkGenericClassInNonGenericObjectType'.
  ```
  because `phpstan.neon` uses PHPStan 1.x keys while `vendor/bin/phpstan --version` reports **2.1.7**. Do **not** try to fix this as part of this task; note it in the PR description.
- RoadRunner caches code in memory; after PHP changes run `docker compose exec -T roadrunner rr -c /etc/.rr.yaml reset` (verified working).
- There are **no JS tests** in this repo (`tests/js` does not exist; `frontend/package.json` has no `test` script). The `npm run test` line in `AGENTS.md` is stale. Verification of the frontend is by build + browser inspection.

---

## Architecture / proposed approach

The old client-side approach (`$root.$emit` bus + axios JSON endpoints that swapped `this.products`) has no Vue 3 equivalent and bypasses Inertia. Instead, the sidebar becomes a **pure URL-state controller**: clicking a category or a filter navigates Inertia to `/shop?category_id=<id>&values_<propertyId>=<csv>` (preserving scroll, resetting `page`), and `ShopController::list()` reads those params and paginates the result server-side. To avoid duplicating the existing `values_*` → DTO parsing, that logic is extracted out of `SearchController` into `app/Services/Filter/ProductFilterParser.php`, and `ProductRepository` gains a `buildFilteredQuery()` that both the existing collection method and a new paginating method share. All existing filter **semantics** (DTO shapes, `whereHas('properties')` clauses, `Catalog::getCatalogIdsTree`) are preserved untouched — only the delivery mechanism changes.

---

## Pre-flight (do these first, in order)

**P1. Confirm the container and code path are live.**

```bash
cd /Users/mac_mac/projects/lara_shop
docker compose exec -T roadrunner php artisan --version
```
Expected (ignore the two `level=warning` compose-file lines that precede it):
```
Laravel Framework 10.x.x
```

**P2. Confirm the test runner works before you change anything.**

```bash
docker compose exec -T roadrunner php artisan test --filter=testGetShopPage
```
Expected:
```
   PASS  Tests\Feature\HttpGetShopTest
  ✓ get shop page                                                        1.61s

  Tests:    1 passed (12 assertions)
```
If this fails, stop — the environment is broken, not your change.

**P3. Record the PHPStan baseline failure** (so you can prove you did not introduce it):

```bash
./vendor/bin/phpstan analyse --memory-limit=2G --no-progress -c phpstan.neon ./ 2>&1 | tail -5
```
Expected (pre-existing, **do not fix**):
```
Invalid configuration:
Unexpected item 'parameters › checkMissingIterableValueType'.
Invalid configuration:
Unexpected item 'parameters › checkGenericClassInNonGenericObjectType'.
```

**P4. Seed the dev database** so the sidebar has something to render.
This is a **destructive dev-DB action** (`CatalogsTableSeeder` and friends call `DB::table(...)->truncate()`), acceptable here because the DB is empty — confirm with the user if it is not.

```bash
docker compose exec -T roadrunner php artisan db:seed --class=DatabaseSeeder
docker compose exec -T roadrunner php artisan tinker --execute="echo 'catalogs='.\App\Catalog::count().' roots='.\App\Catalog::whereNull('parent_id')->count().' properties='.\App\Property::count().' products='.\App\Product::count().PHP_EOL;"
```
Expected second command (last line):
```
catalogs=16 roots=4 properties=2 values=N products=199
```
`catalogs=16 roots=4 properties=2 products=199` are exact — derived from `CatalogsTableSeeder` (4 roots `Appliances/Furniture/Food/Jewelry`, each with 3 children: `TVs/Refrigerators/Cellphones`, `Sofas/Cupboards/Beds`, `Milk/…`, `…`) and `ProductsTableSeeder` (`for ($i=1; $i<200; $i++)` → 199 rows, random `catalog_id` in 1..16). `values=N` depends on `PropertyValuesTableSeeder`; just check it is > 0.

**P5. Confirm the page loads and note the current prop list.**

```bash
curl -s 'http://localhost/shop' | python3 -c "import sys,html,json,re; m=re.search(r'data-page=\"(.*?)\"', sys.stdin.read(), re.S); print(list(json.loads(html.unescape(m.group(1)))['props'].keys()))"
```
Expected **before** any change:
```
['errors', 'appName', 'flash', 'auth', 'userName', 'cart', 'products', 'links']
```
After this plan is implemented, `categories` and `properties` must appear in that list. This is your end-to-end acceptance check.

---

## Step-by-step tasks

Work in this order. Each backend task is a RED → GREEN cycle. Commit after each task (frequent commits).

---

### Task 1 — Add the `children()` relation to `Catalog`

**Why**: the sidebar needs a 2-level category tree. `Catalog` has `parent_id` but no relation, so a tree cannot be eager-loaded.

**File**: `app/Catalog.php`

Add `use Illuminate\Database\Eloquent\Relations\HasMany;` if not present (it already is, line 8), then add this method immediately after `products()`:

```php
    /**
     * Direct child catalogs, ordered for display.
     *
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany('App\Catalog', 'parent_id', 'id')->orderBy('priority');
    }
```

**Verify** (no test needed — this is a one-line relation exercised by Task 4's tests):

```bash
docker compose exec -T roadrunner php artisan tinker --execute="\$c = \App\Catalog::whereNull('parent_id')->orderBy('priority')->with('children')->first(); echo \$c->name.' -> '.\$c->children->pluck('name')->implode(', ').PHP_EOL;"
```
Expected: `Appliances -> TVs, Refrigerators, Cellphones` (children ordered by `priority`). This works only after P4 has seeded the DB; if it prints `Appliances -> ` with no children, the seed step was skipped.

**Commit**:
```bash
git add app/Catalog.php
git commit -m "feat(catalog): add children() relation for category tree"
```

---

### Task 2 — Extract `ProductFilterParser` and de-bug the `values_*` parsing

**Why**: `ShopController::list()` must parse the same `values_<propertyId>` query params that `/filter` parses. Copy-pasting `SearchController::prepareFilters()` would violate DRY, and it has a real bug: it casts *every* key to an int and calls `Property::findOrFail()`, so a request like `/filter?page=2` becomes `findOrFail(0)` → 404. The shop page always sends `page`, so this must be fixed.

**Files**:
- new `app/Services/Filter/ProductFilterParser.php`
- `app/Http/Controllers/SearchController.php`

**Step 2a — write the failing tests first.**

Create `tests/Feature/ProductFilterParserTest.php`:

```php
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
```

Run it — it must fail with "Class not found":

```bash
docker compose exec -T roadrunner php artisan test --filter=ProductFilterParserTest
```
Expected:
```
   FAIL  Tests\Feature\ProductFilterParserTest
  ⨯ it parses selector and number filters
  ⨯ it ignores non filter and unknown keys
```
(and a `Target class [App\Services\Filter\ProductFilterParser] does not exist`-style error).

**Step 2b — create the parser.**

Create `app/Services/Filter/ProductFilterParser.php`:

```php
<?php

declare(strict_types=1);

namespace App\Services\Filter;

use App\DTO\FilterNumberDTO;
use App\DTO\FilterSelectorDTO;
use App\Property;
use Illuminate\Support\Collection;

/**
 * Turns raw query-string filters of the form
 *   values_<propertyId>=1,2        (selector -> property_value ids)
 *   values_<propertyId>=10,50      (number   -> min,max)
 * into the DTOs consumed by ProductRepository::getFilteredProducts().
 *
 * Any key that is not `values_*` (page, category_id, sort, ...) and any
 * unknown property id is ignored instead of raising a 404.
 */
class ProductFilterParser
{
    /**
     * @param array<string, mixed> $filters
     * @return Collection<int, FilterSelectorDTO|FilterNumberDTO>
     */
    public function parse(array $filters): Collection
    {
        $properties = new Collection();

        foreach ($filters as $key => $filterValues) {
            $key = (string) $key;

            if (!str_starts_with($key, 'values_')) {
                continue;
            }

            if ($filterValues === null || $filterValues === '') {
                continue;
            }

            $property = Property::find((int) str_replace('values_', '', $key));

            if (!$property instanceof Property) {
                continue;
            }

            $values = is_array($filterValues)
                ? $filterValues
                : explode(',', (string) $filterValues);

            if ($property->type === Property::TYPE_SELECTOR) {
                $values = array_map('intval', $values);
                $values = array_values(array_filter($values, static fn (int $id): bool => $id > 0));
                $properties->add(new FilterSelectorDTO($values, $property->id));
            } elseif ($property->type === Property::TYPE_NUMBER) {
                $properties->add(new FilterNumberDTO(
                    (float) ($values[0] ?? 0),
                    (float) ($values[1] ?? 0),
                    $property->id
                ));
            }
        }

        return $properties;
    }
}
```

Run the tests again — must pass:

```bash
docker compose exec -T roadrunner php artisan test --filter=ProductFilterParserTest
```
Expected:
```
   PASS  Tests\Feature\ProductFilterParserTest
  ✓ it parses selector and number filters
  ✓ it ignores non filter and unknown keys

  Tests:    2 passed
```

**Step 2c — lock the existing `/filter` endpoint with regression tests.**

Create `tests/Feature/FilterEndpointTest.php`:

```php
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
```

Run against the **current** (unmodified) `SearchController` — `testItIgnoresNonFilterQueryParams` must FAIL, proving the bug:

```bash
docker compose exec -T roadrunner php artisan test --filter=FilterEndpointTest
```
Expected:
```
   FAIL  Tests\Feature\FilterEndpointTest
  ✓ it returns products matching a selector value
  ⨯ it ignores non filter query params
  ───
  FAILED  Tests\Feature\FilterEndpointTest > it ignores non filter query params
  ... 404 Not Found
```

**Step 2d — refactor `SearchController` to use the parser.**

In `app/Http/Controllers/SearchController.php`:

1. Replace the import block so `App\DTO\FilterNumberDTO`, `App\DTO\FilterSelectorDTO`, `App\Property` and `Illuminate\Support\Collection` are removed, and `App\Services\Filter\ProductFilterParser` is added. The final import block must be exactly:

```php
use App\Catalog;
use App\Http\Resources\ProductCollection;
use App\Repositories\ProductRepository;
use App\Services\Filter\ProductFilterParser;
use Illuminate\Http\Request;
use App\Product;
use Inertia\Inertia;
use Inertia\Response;
```

2. Replace the whole `filter()` method plus the private `prepareFilters()` method (lines 33–77) with:

```php
    public function filter(
        Request $request,
        Catalog $catalog,
        ProductRepository $productRepository,
        ProductFilterParser $filterParser
    ): ProductCollection
    {
        $filters = $request->all();

        $catalog_ids = isset($filters['category_id']) ? $catalog->getCatalogIdsTree((int)$filters['category_id']) : [];
        unset($filters['category_id']);

        $filters = $filterParser->parse($filters);
        $products = $productRepository->getFilteredProducts($filters, $catalog_ids);

        return new ProductCollection($products);
    }
```

`search()` must remain byte-for-byte unchanged.

Run:
```bash
docker compose exec -T roadrunner php artisan test --filter='FilterEndpointTest|ProductFilterParserTest'
```
Expected:
```
   PASS  Tests\Feature\FilterEndpointTest
  ✓ it returns products matching a selector value
  ✓ it ignores non filter query params
   PASS  Tests\Feature\ProductFilterParserTest
  ✓ it parses selector and number filters
  ✓ it ignores non filter and unknown keys

  Tests:    4 passed
```

Then confirm you broke nothing that already existed:
```bash
docker compose exec -T roadrunner php artisan test --filter='ProductRepositoryTest|testGetShopPage'
```
Expected: both PASS.

**Commit**:
```bash
git add app/Services/Filter/ProductFilterParser.php app/Http/Controllers/SearchController.php tests/Feature/ProductFilterParserTest.php tests/Feature/FilterEndpointTest.php
git commit -m "refactor(filter): extract ProductFilterParser, ignore non-filter query params"
```

---

### Task 3 — Add a paginating filtered query to `ProductRepository`

**Why**: `getFilteredProducts()` hard-limits to `Product::LIST_LIMIT` and returns a plain collection, so it cannot drive a paginated shop page. The query-building body must be shared, not duplicated.

**File**: `app/Repositories/ProductRepository.php`

Add `use Illuminate\Contracts\Pagination\LengthAwarePaginator;` to the imports, then replace the existing `getFilteredProducts()` method (lines 18–51, keeping the docblock above it) with these three methods:

```php
    /**
     * Shared filter query builder: category subtree + property filters.
     *
     * @param Collection $filters
     * @param int[]|null $category_ids
     * @return Builder
     */
    public function buildFilteredQuery(Collection $filters, ?array $category_ids = []): Builder
    {
        $query = $this->mProduct->newQuery()
            ->when(!empty($category_ids), function ($query) use ($category_ids) {
                $query->whereIn('catalog_id', $category_ids);
            });
        foreach ($filters as $filterDto) {
            $query->whereHas('properties', function ($q) use ($filterDto) {
                $q->when($filterDto instanceof FilterNumberDTO, function (Builder $nQuery) use ($filterDto) {
                    $nQuery->where('property_values.property_id', $filterDto->getId());
                    if ($filterDto->getMinValue()) {
                        $nQuery->whereRaw('CAST(property_values.value as DECIMAL) >= ?')
                            ->addBinding($filterDto->getMinValue());
                    }
                    if ($filterDto->getMaxValue()) {
                        $nQuery->whereRaw('CAST(property_values.value as DECIMAL) <= ?')
                            ->addBinding($filterDto->getMaxValue());
                    }
                })
                ->when($filterDto instanceof FilterSelectorDTO, function ($sQuery) use ($filterDto) {
                    if ($filterDto->getValues()) {
                        $sQuery->whereIn('property_value_id', $filterDto->getValues());
                    }
                });
            });
        }

        return $query;
    }

    /**
     * @param int[]|null $category_ids
     * @param Collection $filters
     * @return EloquentCollection
     */
    public function getFilteredProducts(Collection $filters, ?array $category_ids = []): EloquentCollection
    {
        return $this->buildFilteredQuery($filters, $category_ids)
            ->limit($this->mProduct::LIST_LIMIT)
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Same filters, but paginated so the shop page keeps its paginator.
     *
     * @param int[]|null $category_ids
     * @param Collection $filters
     * @return LengthAwarePaginator
     */
    public function paginateFilteredProducts(
        Collection $filters,
        ?array $category_ids = [],
        int $perPage = Product::LIST_LIMIT
    ): LengthAwarePaginator {
        return $this->buildFilteredQuery($filters, $category_ids)
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);
    }
```

Note: `paginate()` returns a `LengthAwarePaginator`, so `$products->links()` and `products.total` / `per_page` / `current_page` all keep working exactly as `ShopPagination.vue` and `ProductList.vue` expect.

**Verify — no new test needed; the existing `ProductRepositoryTest` is the RED/GREEN guard for the refactor:**

```bash
docker compose exec -T roadrunner php artisan test --filter=ProductRepositoryTest
```
Expected:
```
   PASS  Tests\Feature\Repositories\ProductRepositoryTest
  ✓ get filtered products

  Tests:    1 passed
```
If this fails, your refactor changed filter semantics — revert and redo without altering the `whereHas` clauses.

**Commit**:
```bash
git add app/Repositories/ProductRepository.php
git commit -m "refactor(product): share filtered query, add paginated variant"
```

---

### Task 4 — Teach `ShopController::list()` to filter and to pass sidebar props

**Why**: this is the actual backend change that makes the sidebar real.

**Files**: `app/Http/Controllers/ShopController.php`, new `tests/Feature/ShopFiltersTest.php`.

**Step 4a — write the failing tests.**

Create `tests/Feature/ShopFiltersTest.php`. Note it deliberately does **not** reuse `HttpGetShopTest::setUp()` (which creates 10 random root catalogs and would make the counts noisy):

```php
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
```

Run — all three must fail (no `categories`/`properties` props; no filtering):

```bash
docker compose exec -T roadrunner php artisan test --filter=ShopFiltersTest
```
Expected:
```
   FAIL  Tests\Feature\ShopFiltersTest
  ⨯ shop page exposes category tree and filter properties
  ⨯ shop page filters products by category subtree
  ⨯ shop page filters products by property value

  Tests:    3 failed
```

**Step 4b — implement.**

In `app/Http/Controllers/ShopController.php`:

1. Add imports (keep the existing ones):

```php
use App\Repositories\ProductRepository;
use App\Repositories\PropertyRepository;
use App\Services\Filter\ProductFilterParser;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
```

2. Replace `list()` (lines 30–41) with:

```php
    public function list(
        Request $request,
        ProductRepository $productRepository,
        PropertyRepository $propertyRepository,
        ProductFilterParser $filterParser
    ): Response {
        $query = $request->all();

        $categoryId = isset($query['category_id']) && (int) $query['category_id'] > 0
            ? (int) $query['category_id']
            : null;

        $catalogIds = $categoryId !== null
            ? $this->catalog->getCatalogIdsTree($categoryId)
            : [];

        $products = $productRepository
            ->paginateFilteredProducts($filterParser->parse($query), $catalogIds, Product::LIST_LIMIT)
            ->withQueryString();

        return Inertia::render('ProductList', [
            'products' => $products,
            'categories' => $this->catalogTree(),
            'properties' => $propertyRepository->getFilteredProducts(),
        ]);
    }
```

3. Add this private helper method just above `getProduct()`:

```php
    /**
     * Root catalogs with their direct children, ordered for the sidebar.
     *
     * @return EloquentCollection
     */
    private function catalogTree(): EloquentCollection
    {
        return $this->catalog->newQuery()
            ->whereNull('parent_id')
            ->with(['children' => static fn ($query) => $query->orderBy('priority')])
            ->orderBy('priority')
            ->get(['id', 'name', 'parent_id']);
    }
```

Note: `getCatalogIdsTree()` already recurses the full subtree, so selecting a root category matches products in its children too — that is what `testShopPageFiltersProductsByCategorySubtree` asserts.

**Step 4c — run.**

```bash
docker compose exec -T roadrunner php artisan test --filter=ShopFiltersTest
```
Expected:
```
   PASS  Tests\Feature\ShopFiltersTest
  ✓ shop page exposes category tree and filter properties
  ✓ shop page filters products by category subtree
  ✓ shop page filters products by property value

  Tests:    3 passed
```

Then the whole suite:
```bash
docker compose exec -T roadrunner php artisan test
```
Expected: `Tests:  N passed` with **no failures**. (Baseline before your change is the same suite minus your 9 new tests; if `RelatedProductRepositoryTest` or `OrderControllerTest` were already red, note it and do not fix it here.)

Then reset RoadRunner and re-check the live props:
```bash
docker compose exec -T roadrunner rr -c /etc/.rr.yaml reset
curl -s 'http://localhost/shop' | python3 -c "import sys,html,json,re; m=re.search(r'data-page=\"(.*?)\"', sys.stdin.read(), re.S); print(list(json.loads(html.unescape(m.group(1)))['props'].keys()))"
```
Expected (note the two new keys):
```
['errors', 'appName', 'flash', 'auth', 'userName', 'cart', 'products', 'categories', 'properties']
```

**Commit**:
```bash
git add app/Http/Controllers/ShopController.php tests/Feature/ShopFiltersTest.php
git commit -m "feat(shop): filter products by category and property, share sidebar data"
```

---

### Task 5 — Delete the dead `/shop/properties` route

**Why**: `routes/web.php:45` maps `GET /shop/properties` to `ShopController@getFilterProperties`, a method that **does not exist** on `ShopController`. It 500s, it is unreachable from any UI, and it shadows nothing (properties now arrive as an Inertia prop). Leaving it invites the next reader to wire the sidebar to a broken endpoint.

**File**: `routes/web.php`

Remove this line from the `shop` route group (currently line 45):

```php
    Route::get('/properties', 'ShopController@getFilterProperties');
```

Keep `Route::get('/filter/properties', 'FilterController@getFilterProperties')` — that one is real.

**Verify** — before the change, `curl -s -o /dev/null -w '%{http_code}\n' http://localhost/shop/properties` prints `500` and the route table contains a `shop/properties` row:

```
  GET|HEAD  filter/properties get-filter-properties › FilterController@getFil…
  GET|HEAD  shop/properties ............... ShopController@getFilterProperties
```

After removing the line:

```bash
docker compose exec -T roadrunner php artisan route:list 2>/dev/null | grep -i properties
```
Expected — the `shop/properties` row is gone, `filter/properties` remains:
```
  POST      admin/product/property-type AdminPropertiesController@addProperty…
  GET|HEAD  admin/product/{product_id}/properties AdminProductsController@get…
  GET|HEAD  admin/products/property-types AdminPropertiesController@getProper…
  GET|HEAD  admin/products/property/{id}/values AdminPropertiesController@get…
  POST      admin/properties ........ AdminPropertiesController@createProperty
  GET|HEAD  filter/properties get-filter-properties › FilterController@getFil…
```
(the exact truncation/column padding varies; the requirement is only that no line contains `shop/properties`)

```bash
docker compose exec -T roadrunner rr -c /etc/.rr.yaml reset
curl -s -o /dev/null -w '%{http_code}\n' http://localhost/shop/properties
```
Expected: `404` (previously `500`).

**Commit**:
```bash
git add routes/web.php
git commit -m "fix(routes): drop dead /shop/properties route"
```

---

### Task 6 — Create the `ShopSidebar` component

**Why**: this is the left column itself.

**File**: new `frontend/src/Components/ShopSidebar.vue`

The component is **stateless with respect to filters**: it reads the active filter state out of the current Inertia URL (`usePage().url`) and navigates on interaction. That means the URL is the single source of truth — no prop drilling, no duplicated state, and a bookmarked/refreshed filtered URL renders correctly.

Create `frontend/src/Components/ShopSidebar.vue` with exactly:

```vue
<script setup>
import { computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps({
  categories: { type: Array, default: () => [] },
  properties: { type: Array, default: () => [] },
})

const page = usePage()

// The current query string, e.g. /shop?category_id=3&values_7=1%2C2&page=2
const searchParams = computed(() => new URL(page.url, window.location.origin).searchParams)

const activeCategoryId = computed(() => Number(searchParams.value.get('category_id')) || null)

// values_7=1,2 -> { '7': ['1','2'] }
const activeValues = computed(() => {
  const map = {}
  for (const [key, raw] of searchParams.value.entries()) {
    if (key.startsWith('values_')) {
      map[key.replace('values_', '')] = raw.split(',')
    }
  }
  return map
})

const isChecked = (propertyId, valueId) =>
  (activeValues.value[propertyId] || []).includes(String(valueId))

const rangeValue = (propertyId, index) =>
  (activeValues.value[propertyId] || [])[index] ?? ''

/**
 * Merge `params` into the current /shop query and navigate.
 * Always drops `page` so a filter change restarts pagination.
 */
function navigate(params) {
  const search = new URLSearchParams(new URL(page.url, window.location.origin).search)
  search.delete('page')

  Object.entries(params).forEach(([key, value]) => {
    const empty =
      value === null || value === undefined || value === '' || (Array.isArray(value) && value.length === 0)
    if (empty) {
      search.delete(key)
    } else {
      search.set(key, Array.isArray(value) ? value.join(',') : String(value))
    }
  })

  const query = search.toString()
  router.get(`/shop${query ? `?${query}` : ''}`, {}, { preserveScroll: true, preserveState: true })
}

function selectCategory(id) {
  navigate({ category_id: id })
}

function toggleValue(propertyId, valueId) {
  const current = [...(activeValues.value[propertyId] || [])]
  const index = current.indexOf(String(valueId))
  if (index >= 0) {
    current.splice(index, 1)
  } else {
    current.push(String(valueId))
  }
  navigate({ [`values_${propertyId}`]: current })
}

function setRange(propertyId, index, raw) {
  const current = [...(activeValues.value[propertyId] || [])]
  while (current.length < 2) {
    current.push('')
  }
  current[index] = raw
  navigate({ [`values_${propertyId}`]: current })
}

function clearAll() {
  router.get('/shop', {}, { preserveScroll: true })
}
</script>

<template>
  <aside class="shop-sidebar">
    <!-- Categories -->
    <section class="sidebar-block">
      <h2 class="sidebar-title">Categories</h2>
      <ul class="category-list">
        <li>
          <button
            type="button"
            class="category-link"
            :class="{ 'is-active': activeCategoryId === null }"
            @click="selectCategory(null)"
          >
            All products
          </button>
        </li>
        <li v-for="category in categories" :key="category.id">
          <button
            type="button"
            class="category-link"
            :class="{ 'is-active': activeCategoryId === category.id }"
            @click="selectCategory(category.id)"
          >
            {{ category.name }}
          </button>
          <ul v-if="category.children?.length" class="category-sublist">
            <li v-for="child in category.children" :key="child.id">
              <button
                type="button"
                class="category-link category-link--child"
                :class="{ 'is-active': activeCategoryId === child.id }"
                @click="selectCategory(child.id)"
              >
                {{ child.name }}
              </button>
            </li>
          </ul>
        </li>
      </ul>
    </section>

    <!-- Property filters -->
    <section v-if="properties.length" class="sidebar-block">
      <h2 class="sidebar-title">Filters</h2>

      <div v-for="property in properties" :key="property.id" class="filter-group">
        <p class="filter-group__name">{{ property.name }}</p>

        <template v-if="property.type === 'selector'">
          <label
            v-for="value in property.property_values"
            :key="value.id"
            class="filter-checkbox"
          >
            <input
              type="checkbox"
              :checked="isChecked(property.id, value.id)"
              @change="toggleValue(property.id, value.id)"
            />
            <span>{{ value.value }}</span>
          </label>
        </template>

        <div v-else class="filter-range">
          <input
            type="number"
            class="filter-range__input"
            placeholder="min"
            :value="rangeValue(property.id, 0)"
            @change="setRange(property.id, 0, $event.target.value)"
          />
          <input
            type="number"
            class="filter-range__input"
            placeholder="max"
            :value="rangeValue(property.id, 1)"
            @change="setRange(property.id, 1, $event.target.value)"
          />
        </div>
      </div>

      <button type="button" class="sidebar-clear" @click="clearAll">Clear all filters</button>
    </section>
  </aside>
</template>

<style scoped>
.shop-sidebar {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.sidebar-block {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 1.15rem 1.25rem;
}

.sidebar-title {
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #64748b;
  margin: 0 0 0.85rem;
}

.category-list,
.category-sublist {
  list-style: none;
  margin: 0;
  padding: 0;
}

.category-sublist {
  margin: 0.15rem 0 0.35rem 0.85rem;
  border-left: 1px solid #e2e8f0;
  padding-left: 0.6rem;
}

.category-link {
  display: block;
  width: 100%;
  text-align: left;
  background: none;
  border: none;
  padding: 0.3rem 0.35rem;
  border-radius: 6px;
  font: inherit;
  font-size: 0.9rem;
  color: #334155;
  cursor: pointer;
}

.category-link:hover {
  background: #f1f5f9;
  color: #0f172a;
}

.category-link.is-active {
  color: #0284c7;
  font-weight: 600;
  background: #f0f9ff;
}

.category-link--child {
  font-size: 0.85rem;
  color: #64748b;
}

.filter-group {
  margin-bottom: 1.1rem;
}

.filter-group:last-of-type {
  margin-bottom: 0.5rem;
}

.filter-group__name {
  font-size: 0.9rem;
  font-weight: 600;
  color: #0f172a;
  margin: 0 0 0.5rem;
  text-transform: capitalize;
}

.filter-checkbox {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: #334155;
  padding: 0.15rem 0;
  cursor: pointer;
}

.filter-checkbox input {
  accent-color: #0284c7;
}

.filter-range {
  display: flex;
  gap: 0.5rem;
}

.filter-range__input {
  width: 100%;
  min-width: 0;
  height: 34px;
  padding: 0 0.6rem;
  font: inherit;
  font-size: 0.85rem;
  color: #0f172a;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  outline: none;
}

.filter-range__input:focus {
  background: #ffffff;
  border-color: #0284c7;
  box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
}

.sidebar-clear {
  margin-top: 0.5rem;
  background: none;
  border: none;
  padding: 0;
  font: inherit;
  font-size: 0.8rem;
  font-weight: 600;
  color: #0284c7;
  cursor: pointer;
}

.sidebar-clear:hover {
  text-decoration: underline;
}
</style>
```

> `props` is declared but unused inside `<script setup>` — that is fine and intentional (it documents the contract). If a linter complains, change `const props = defineProps({...})` to `defineProps({...})`.

**Verify**: there is no test runner for `.vue`, so verification is the build in Task 8. Do a syntax sanity check now by building:

```bash
cd frontend && npm run build 2>&1 | tail -12
```
Expected: ends with something like
```
✓ built in 3.42s
```
and **no** `[vue/compiler-sfc]` error. A Vue template error would print `Error: ...` and exit non-zero.

**Commit** (build output goes in Task 8's commit):
```bash
git add frontend/src/Components/ShopSidebar.vue
git commit -m "feat(shop): add ShopSidebar with categories and property filters"
```

---

### Task 7 — Give `ProductList.vue` its two-column layout back

**File**: `frontend/src/Pages/ProductList.vue`

Replace the **entire** file with:

```vue
<template>
  <div class="shop-page px-3 py-4">
    <!-- Header title and count -->
    <div class="flex align-items-center justify-content-between mb-4 pb-2 border-bottom-1 border-surface-200">
      <div>
        <h1 class="text-3xl font-bold text-900 m-0">Products</h1>
        <p class="text-sm text-500 m-0 mt-1">
          Showing {{ products?.from || 0 }} - {{ products?.to || 0 }} of {{ products?.total || 0 }} items
        </p>
      </div>
    </div>

    <!-- Left column (categories + filters) + product grid -->
    <div class="shop-layout">
      <ShopSidebar :categories="categories" :properties="properties" />

      <div class="shop-layout__main">
        <div v-if="productItems.length" class="grid">
          <div
            v-for="product in productItems"
            :key="product.id"
            class="col-12 sm:col-6 xl:col-4 flex"
          >
            <ProductCard :product="product" />
          </div>
        </div>

        <div v-else class="shop-empty">
          <i class="pi pi-search text-3xl text-400"></i>
          <p class="m-0 mt-2 text-500">No products match the selected filters.</p>
        </div>

        <!-- Pagination -->
        <div class="mt-5 flex justify-content-center">
          <ShopPagination :paginator="products" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ProductCard from "../Components/ProductCard.vue";
import Layout from "../Layouts/AppLayout.vue";
import ShopPagination from "../Components/ShopPagination.vue";
import ShopSidebar from "../Components/ShopSidebar.vue";

export default {
  name: 'ProductList',
  components: { ProductCard, ShopPagination, ShopSidebar },
  props: {
    keyword: String,
    category: [String, Number],
    products: [Object, Array],
    categories: { type: Array, default: () => [] },
    properties: { type: Array, default: () => [] },
  },
  computed: {
    // /shop and /search both render this page; /search passes a plain array.
    productItems() {
      return this.products?.data || this.products || [];
    },
  },
  layout: Layout,
}
</script>

<style scoped>
.shop-page {
  max-width: 1280px;
  margin: 0 auto;
}

.shop-layout {
  display: grid;
  grid-template-columns: 260px minmax(0, 1fr);
  gap: 2rem;
  align-items: start;
}

.shop-layout__main {
  min-width: 0;
}

.shop-empty {
  text-align: center;
  padding: 4rem 1rem;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
}

@media (max-width: 991px) {
  .shop-layout {
    grid-template-columns: minmax(0, 1fr);
    gap: 1.25rem;
  }
}
</style>
```

Notes on deliberate choices:

- `col-4` → `xl:col-4`: with a 260 px sidebar the previous `xl:col-3` cards became too narrow. `sm:col-6 xl:col-4` keeps the pre-sidebar card width.
- The `links` prop was dropped — nothing reads it (`ShopPagination` reads `products.total` / `per_page` / `current_page`). `ShopController` no longer sends it (Task 4). `/search` never sent it.
- The `ShopSidebar` is rendered even when `categories`/`properties` are empty, so the "All products" reset stays reachable.

**Verify**:
```bash
cd frontend && npm run build 2>&1 | tail -12
```
Expected: `✓ built in ...` with no errors.

**Commit**:
```bash
git add frontend/src/Pages/ProductList.vue
git commit -m "feat(shop): restore left column with categories and filters"
```

---

### Task 8 — Build, reset, and verify end-to-end in a browser

**Step 8a — build and reset.**

```bash
cd /Users/mac_mac/projects/lara_shop/frontend && npm run build
cd /Users/mac_mac/projects/lara_shop
docker compose exec -T roadrunner rr -c /etc/.rr.yaml reset
```
Expected: build prints `✓ built in ...`; reset prints `plugin reset: [http]`.

Confirm the manifest hash changed (proof the new bundle is what gets served):
```bash
python3 -c "import json;print(json.load(open('public/build/manifest.json'))['src/main.js']['file'])"
```
Expected: a `assets/main-*.js` filename **different** from `assets/main-BY1rG0uz.js` (the value before this work).

**Step 8b — verify the sidebar renders and filters.**

Use the browser tool (not curl — this needs real layout and clicks):

1. Open `http://localhost/shop`, screenshot. Assert visually:
   - A white bordered card on the **left** titled **CATEGORIES** listing `All products`, `Appliances`, `Furniture`, `Food`, `Jewelry`, with each root's children indented under it.
   - A second card titled **FILTERS** with `manufacturer` checkboxes and `weight` min/max number inputs.
   - The product grid sits to the **right** of both cards, 3 cards per row on a desktop viewport.
2. Click a root category (e.g. `Appliances`). Assert:
   - URL becomes `http://localhost/shop?category_id=<id>`
   - the category link turns sky-blue/bold
   - the product count in the header drops
   - a `page` param is absent
3. Tick a `manufacturer` checkbox. Assert the URL gains `values_<propertyId>=<valueId>` **and keeps** `category_id`, and the grid re-renders.
4. Type a number into `weight` min. Assert the URL gains `values_<weightPropertyId>=<min>,` and the grid narrows.
5. Click **Clear all filters**. Assert the URL is exactly `http://localhost/shop` and the full grid returns.
6. Narrow the viewport below 992 px. Assert the sidebar stacks **above** the grid (single column).
7. Paste `http://localhost/shop?category_id=<id>&values_<propertyId>=<valueId>` directly into the address bar and load. Assert the corresponding checkbox is already ticked and the category already highlighted (proves URL-as-state, i.e. a bookmarked filtered URL works).

If a step fails, the most likely causes in order: (a) `npm run build` was not re-run, (b) RoadRunner not reset, (c) the DB was not seeded (P4) so `categories`/`properties` are empty arrays and the sidebar renders nothing.

**Step 8c — final full check.**

```bash
docker compose exec -T roadrunner php artisan test
```
Expected: no failures.

```bash
git status --short
```
Expected: only `public/build/` files modified.

**Step 8d — commit the build output** (this repo commits `public/build`, see `e7fb587`):
```bash
git add public/build
git commit -m "build: rebuild assets for shop sidebar"
```

---

## Tests / validation summary

| Layer | Command | Gate |
|---|---|---|
| Parser unit | `docker compose exec -T roadrunner php artisan test --filter=ProductFilterParserTest` | 2 passed |
| `/filter` regression | `... --filter=FilterEndpointTest` | 2 passed |
| Repository refactor guard | `... --filter=ProductRepositoryTest` | 1 passed (pre-existing) |
| Shop page contract | `... --filter=ShopFiltersTest` | 3 passed |
| Whole suite | `docker compose exec -T roadrunner php artisan test` | 0 failures |
| Frontend build | `cd frontend && npm run build` | `✓ built in ...`, exit 0 |
| Live props | `curl -s http://localhost/shop \| python3 -c "...props.keys()"` | includes `categories`, `properties` |
| Dead route | `curl -s -o /dev/null -w '%{http_code}\n' http://localhost/shop/properties` | `404` |
| Manual UI | browser tool, steps 8b.1–8b.7 | all assertions hold |
| PHPStan | **not usable** — pre-existing invalid config (see P3) | n/a, document it |

**TDD discipline**: every backend task above starts with a test that is run and observed to FAIL for the right reason, then is implemented minimally, then re-run to PASS, then committed. Do not skip the RED step — the expected failure output is given for each.

**Frequent commits**: 8 commits, one per task, in the order given.

---

## Risks, tradeoffs, and open questions

**Risks**

1. **`docker compose` resolves the wrong file.** `docker compose exec` works against the running container, but `docker compose up -d` would recreate it from `compose.yaml` — which has **no bind mount** and `.env.prod` — and you would lose the live-reload setup. Never run `up`/`build` as part of this work.
2. **Tests only run in the container.** Running them on the host gives a misleading `mariadb_test` DNS error. Always use the `docker compose exec -T roadrunner` form.
3. **Empty DB.** The dev database currently has zero rows; without P4 the sidebar renders nothing and it will look like a bug.
4. **`withQueryString()`** is applied to the paginator so pagination keeps the filters. `ShopPagination.vue` builds its own URL from `window.location.href` and only sets `page`, so it already preserves filters — the `withQueryString()` is belt-and-braces for the `products.links` payload.
5. **Behavior change in `/filter`**: unknown/stale `values_*` property ids are now silently ignored instead of 404-ing, and non-`values_*` keys no longer 404. This is a deliberate hardening (the old code 404'd on `/filter?page=2`), but it *is* a contract change. `FilterEndpointTest` locks the new contract. If any external caller depended on the 404, that would need to be raised.
6. **`Property::factory()` assigns a random `prop_group_id`** and a random `priority`, and `Catalog::factory()` uses 15 random words for `name`. Tests must set the fields they assert on explicitly (they do).
7. **Category depth**: the sidebar renders exactly two levels (`roots → children`). `Catalog::getCatalogIdsTree()` filters across unlimited depth, so *filtering* is correct for deeper trees, but deeper levels are not *navigable* in the UI. The old `master` UI had the same two-level limit, so this is not a regression.
8. **`orderBy('priority')` with nullable priorities** puts `NULL`s first on MySQL/MariaDB. Pre-existing behavior; seeded catalogs all have explicit priorities.

**Tradeoffs**

- **URL-as-state vs. local component state.** Re-navigating on every checkbox tick means a server round-trip per click. The payoff is that filters are shareable/bookmarkable, the back button works, and there is exactly one source of truth. The old implementation kept state client-side and pushed it through a global event bus, which is why it broke on the Vue 3 upgrade. If per-click latency turns out to be annoying, the fix is to batch selector checkboxes behind an explicit "Apply" button (as the old UI did) — the backend already handles a comma-joined list, so only `ShopSidebar.vue` would change.
- **Range inputs fire on `change`** (blur / Enter), not on every keystroke — deliberate, to avoid a request per character. A `min` with no `max` sends `values_<id>=10,`, which the backend reads as `FilterNumberDTO(10.0, 0.0)`; `0.0` is falsy so no upper bound is applied. Correct, but worth knowing when debugging.
- **Props vs. AJAX.** Reusing the existing `/filter/properties` and `/shop/category/{id}` JSON endpoints would have avoided touching `ShopController`, but it would put a second, non-Inertia data path in the page and would duplicate the filter-state logic. Props were chosen instead. The endpoints stay in place for any other consumer.

**Open questions (confirm with the user before or during implementation)**

1. **Should the sidebar be collapsible on mobile?** The plan stacks it above the grid. A toggle button would be a follow-up.
2. **Should the sidebar show the current category as a breadcrumb** (`Shop › Appliances › Fridges`), like the old `Categories.vue` did above the product grid? The plan renders a nested list only. Adding a breadcrumb is a small addition to `ShopSidebar.vue`.
3. **Should the sidebar hide properties with no products attached?** `PropertyRepository::getFilteredProducts()` returns *all* properties regardless of use, so dead filters can appear. The old UI had the same behavior; filtering the list to "properties actually attached to a product" would be a behavior change and is deliberately out of scope.
4. **`links` prop removal**: `ShopController` no longer sends `links` and `ProductList.vue` no longer declares it. Nothing else in the tree reads it (verified: only `ProductList.vue` ever declared it). Confirm no external consumer.
