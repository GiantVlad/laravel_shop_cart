<?php

namespace Tests\Feature;

use App\Product;
use App\RelatedProduct;
use App\Services\Cart\CartService;
use App\Services\Recommended\Recommended;
use App\ShippingMethod;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp () :void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }
    
    /**
     * A test cart page.
     *
     * @return void
     */
    public function testGetCartPage()
    {
        $response = $this->actingAs($this->user)->get('/cart');
        
        $response->assertSuccessful();
    }
    
    /**
     * A test cart page.
     *
     * @return void
     */
    public function testAddRelated()
    {
        $product = Product::factory()->create();
        
        /** @var RelatedProduct $related */
        $related = RelatedProduct::factory()->create();
        
        $related->products()->attach($product->id);
        
        $response = $this->actingAs($this->user)->postJson('/cart/add-related', ['id' => '1']);
        
        $response->assertSuccessful();
    }
    
    /**
     * A test cart page.
     *
     * @return void
     */
    public function testAddRelatedNoId()
    {
        $product = Product::factory()->create();
        
        /** @var RelatedProduct $related */
        $related = RelatedProduct::factory()->create();
        
        $related->products()->attach($product->id);
        
        $response = $this->actingAs($this->user)->postJson('/cart/add-related', []);
        
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['id']]);
    }
    
    /**
     * A test cart page.
     *
     * @return void
     */
    public function testAddRelatedInvalidId()
    {
        $product = Product::factory()->create();
        
        /** @var RelatedProduct $related */
        $related = RelatedProduct::factory()->create();
        
        $related->products()->attach($product->id);
        
        $response = $this->actingAs($this->user)->postJson('/cart/add-related', ['id' => 123]);
        
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['id']]);
    }
    
    /**
     * @return void
     */
    public function testChangeShippingEmptyCart()
    {
        /** @var ShippingMethod $shippingMethod */
        $shippingMethod = ShippingMethod::factory()->create();
        
        $response = $this->actingAs($this->user)->postJson(
            '/cart/change-shipping',
            ['shippingMethodId' => $shippingMethod->id, 'subtotal' => 100.4]);
        
        $response->assertJsonStructure(['data' => ['items', 'total']]);
        $data = $response->json()['data'];
        $this->assertEquals(0, $data['items']);
        $this->assertEquals(0, $data['total']);
        $response->assertSuccessful();
    }
    
    /**
     * @return void
     */
    public function testChangeShipping()
    {
        $this->withoutExceptionHandling();
        /** @var Product $product */
        $product = Product::factory()->create();
        
        /** @var CartService $cartService */
        $cartService = app()->get(CartService::class);
        $cartService->addToCart($product->id, 2);
        
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
     * @return void
     */
    public function testRemoveItemEmptyCart()
    {
        Product::factory()->create();
        $response = $this->actingAs($this->user)->postJson(
            '/cart/remove-item',
            ['productId' => 1, 'isRelated' => 0, 'subtotal' => 0]);
        
        $response->assertJsonStructure(['data' => ['items', 'total']]);
        $data = $response->json()['data'];
        $this->assertEquals(0, $data['items']);
        $this->assertEquals(0, $data['total']);
        $response->assertSuccessful();
    }
    
    /**
     * @dataProvider removeItemValidatorDP
     * @return void
     */
    public function testRemoveItemValidation($productId, $isRelated, $subtotal, string $errorField)
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
    
    /**
     * @return void
     */
    public function testRemoveItemWithRecommendedAndCartHasOnlyOneProduct()
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
        $cartService->addToCart($product->id, 2);
        
        $response = $this->actingAs($this->user)->postJson(
            '/cart/remove-item',
            ['productId' => $product->id, 'isRelated' => '1', 'subtotal' => 10]);
    
        $response->assertJsonStructure(['data' => ['items', 'total']]);
        $data = $response->json()['data'];
        $this->assertEquals(0, $data['items']);
        $this->assertEquals(0, $data['total']);
        $response->assertSuccessful();
    }
    
    /**
     * @return void
     */
    public function testRemoveItemWithoutRecommendedAndCartHasMoreThanOneProduct()
    {
        $this->withoutExceptionHandling();
        /** @var Product $product */
        $product = Product::factory()->create();
        
        $recommended = $this->createMock(Recommended::class);
        $recommended->expects($this->never())->method('incrementRate');
        app()->instance(Recommended::class, $recommended);
        
        /** @var CartService $cartService */
        $cartService = app()->get(CartService::class);
        $cartService->addToCart($product->id, 2);
    
        /** @var Product $product2 */
        $product2 = Product::factory()->create();
        $cartService->addToCart($product2->id, 1);
        
        $response = $this->actingAs($this->user)->postJson(
            '/cart/remove-item',
            ['productId' => $product->id, 'isRelated' => false, 'subtotal' => 10]);
        
        $response->assertJsonStructure(['data' => ['items', 'total']]);
        $data = $response->json()['data'];
        $this->assertEquals(1, $data['items']);
        $this->assertEquals(10, $data['total']);
        $response->assertSuccessful();
    }
}
