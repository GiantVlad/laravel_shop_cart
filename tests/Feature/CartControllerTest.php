<?php

namespace Tests\Feature;

use App\Library\Services\PaymentResponseInterface;
use App\Library\Services\PaymentServiceInterface;
use App\OrderData;
use App\Product;
use App\RelatedProduct;
use App\Services\Order\OrderStatuses;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Order;

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
}
