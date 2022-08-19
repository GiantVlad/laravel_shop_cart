<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Product;
use App\RelatedProduct;
use App\Services\Cart\CartService;
use App\Services\Recommended\Recommended;
use App\ShippingMethod;
use Tests\TestCase;
use App\User;

class CartControllerTest extends TestCase
{
    private User $user;
    private CartService $cartService;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->cartService = app()->get(CartService::class);
    }
    
    public function tearDown(): void
    {
        parent::tearDown();
        $this->cartService->forget($this->user->id);
    }
    
    public function testGetCartPage(): void
    {
        $response = $this->actingAs($this->user)->get('/cart');

        $response->assertSuccessful();
    }

    public function testAddRelated(): void
    {
        $product = Product::factory()->create();

        /** @var RelatedProduct $related */
        $related = RelatedProduct::factory()->create();

        $related->products()->attach($product->id);

        $response = $this->actingAs($this->user)->postJson('/cart/add-related', ['id' => '1']);

        $response->assertSuccessful();
    }

    public function testAddRelatedNoId(): void
    {
        $product = Product::factory()->create();

        /** @var RelatedProduct $related */
        $related = RelatedProduct::factory()->create();

        $related->products()->attach($product->id);

        $response = $this->actingAs($this->user)->postJson('/cart/add-related', []);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['id']]);
    }
    
    public function testAddRelatedInvalidId(): void
    {
        $product = Product::factory()->create();

        /** @var RelatedProduct $related */
        $related = RelatedProduct::factory()->create();

        $related->products()->attach($product->id);

        $response = $this->actingAs($this->user)->postJson('/cart/add-related', ['id' => 123]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['id']]);
    }

    public function testChangeShippingEmptyCart(): void
    {
        /** @var ShippingMethod $shippingMethod */
        $shippingMethod = ShippingMethod::factory()->create(['enable' => true]);

        $response = $this->actingAs($this->user)->postJson(
            '/cart/change-shipping',
            ['shippingMethodId' => $shippingMethod->id, 'subtotal' => 100.4]);

        $response->assertJsonStructure(['data' => ['items', 'total']]);
        $data = $response->json()['data'];
        $this->assertEquals(0, $data['items']);
        $this->assertEquals(0, $data['total']);
        $response->assertSuccessful();
    }

    public function testChangeShipping(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();

        /** @var CartService $cartService */
        $cartService = app()->get(CartService::class);
        $cartService->addToCart($this->user->id, $product->id, 2);

        /** @var ShippingMethod $shippingMethod */
        $shippingMethod = ShippingMethod::factory()->create([
            'enable' => 1
        ]);

        $response = $this->actingAs($this->user)->postJson(
            '/cart/change-shipping',
            ['shippingMethodId' => $shippingMethod->id, 'subtotal' => 100.45]);

        $response->assertJsonStructure(['data' => ['items', 'total']]);
        $data = $response->json()['data'];
        $this->assertEquals(1, $data['items']);
        $this->assertEquals(100.45, $data['total']);
        $response->assertSuccessful();
    }
    
    /**
     * @dataProvider shippingMethodValidatorDP
     */
    public function testChangeShippingValidation
    (
        int|string|null $shippingMethodId,
        float|string|null $subtotal,
        string $invalidField,
        ?\Closure $shippingMethodCreator = null,
        ?bool $shippingMethodEnabled = true,
    ): void {
        if ($shippingMethodCreator) {
            $shippingMethodCreator($shippingMethodEnabled);
        }
        $response = $this->actingAs($this->user)->postJson(
            '/cart/change-shipping',
            ['shippingMethodId' => $shippingMethodId, 'subtotal' => $subtotal]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => [$invalidField]]);
    }

    public function shippingMethodValidatorDP(): \Generator
    {
        $callback = function (bool $enabled = true) {
            ShippingMethod::query()->delete();
            ShippingMethod::factory()->create([
                'enable' => $enabled
            ]);
        };
        yield 'shipping id null' => [null, 100.56, 'shippingMethodId'];
        yield 'shipping id string' => ['12a', 100.56, 'shippingMethodId'];
        yield 'shipping id is not exists' => [1, 100.56, 'shippingMethodId'];
        yield 'shipping is disabled' => [1, 100.56, 'shippingMethodId', $callback, false];
        yield 'subtotal is null' => [1, null, 'subtotal', $callback];
        yield 'subtotal is not number' => [1, 'a1001b', 'subtotal', $callback];
        yield 'subtotal is less than 0' => [1, -100.45, 'subtotal', $callback];
    }

    public function testRemoveItemEmptyCart(): void
    {
        $product = Product::factory()->create();
        $response = $this->actingAs($this->user)->postJson(
            '/cart/remove-item',
            ['productId' => $product->id, 'isRelated' => 0, 'subtotal' => 0]);

        $response->assertJsonStructure(['data' => ['items', 'total']]);
        $data = $response->json()['data'];
        $this->assertEquals(0, $data['items']);
        $this->assertEquals(0, $data['total']);
        $response->assertSuccessful();
    }

    /**
     * @dataProvider removeItemValidatorDP
     */
    public function testRemoveItemValidation(?int $productId, ?int $isRelated, ?int $subtotal, string $errorField): void
    {
        Product::factory()->create();

        $response = $this->actingAs($this->user)->postJson(
            '/cart/remove-item',
            ['productId' => $productId, 'isRelated' => $isRelated, 'subtotal' => $subtotal]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => [$errorField]]);
    }

    /**
     * @return array[]
     */
    public function removeItemValidatorDP(): array
    {
        return [
            'productId' => [null, 0, 0, 'productId'],
            'isRelated' => [1, null, 0, 'isRelated'],
            'subtotal' => [1, 0, null, 'subtotal'],
        ];
    }
    
    public function testRemoveItemWithRecommendedAndCartHasOnlyOneProduct(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();

        $recommended = $this->createMock(Recommended::class);
        $recommended->expects($this->once())->method('incrementRate')->with(
            $product->id,
            Recommended::RATE_IMPACT_AFTER_REMOVAL_FROM_CART);
        app()->instance(Recommended::class, $recommended);
        
        /** @var CartService $cartService */
        $cartService = app()->get(CartService::class);
        $cartService->addToCart($this->user->id, $product->id, 2);
        $cartProducts = $cartService->getCart($this->user->id);
        
        $this->assertArrayHasKey($product->id, $cartProducts);
        $this->assertCount(4, $cartProducts);
        
        $response = $this->actingAs($this->user)->postJson(
            '/cart/remove-item',
            ['productId' => $product->id, 'isRelated' => '1', 'subtotal' => 10]);
        
        $this->assertNull($cartService->getCart($this->user->id));
        
        $response->assertJsonStructure(['data' => ['items', 'total']]);
        $data = $response->json()['data'];
        $this->assertEquals(0, $data['items']);
        $this->assertEquals(0, $data['total']);
        $response->assertSuccessful();
    }
    
    public function testRemoveItemWithoutRecommendedAndCartHasMoreThanOneProduct(): void
    {
        $this->withoutExceptionHandling();
        /** @var Product $product */
        $product = Product::factory()->create();
        
        $recommended = $this->createMock(Recommended::class);
        $recommended->expects($this->never())->method('incrementRate');
        app()->instance(Recommended::class, $recommended);
        
        /** @var CartService $cartService */
        $cartService = app()->get(CartService::class);
        $cartService->addToCart($this->user->id, $product->id, 2);
    
        /** @var Product $product2 */
        $product2 = Product::factory()->create();
        $cartService->addToCart($this->user->id, $product2->id, 1);
    
        $cartProducts = $cartService->getCart($this->user->id);
    
        $this->assertArrayHasKey($product->id, $cartProducts);
        $this->assertArrayHasKey($product2->id, $cartProducts);
        $this->assertCount(5, $cartProducts);
        
        $response = $this->actingAs($this->user)->postJson(
            '/cart/remove-item',
            ['productId' => $product->id, 'isRelated' => false, 'subtotal' => 10]);
    
        $cartProducts = $cartService->getCart($this->user->id);
        $this->assertArrayNotHasKey($product->id, $cartProducts);
        $this->assertArrayHasKey($product2->id, $cartProducts);
        $this->assertCount(4, $cartProducts);
        
        $response->assertJsonStructure(['data' => ['items', 'total']]);
        $data = $response->json()['data'];
        $this->assertEquals(1, $data['items']);
        $this->assertEquals(10, $data['total']);
        $response->assertSuccessful();
    }
}
