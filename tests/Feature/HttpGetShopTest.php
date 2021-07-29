<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use App\Order;
use App\Catalog;
use App\Product;

class HttpGetShopTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp () :void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        Catalog::factory()->count(10)->create();

        Product::factory()->count(20)->create();

        Order::factory()->create([
            'user_id' => 1,
        ]);
    }
    /**
     * A test home page.
     *
     * @return void
     */
    public function testGetHomePage()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * A test shop page.
     *
     * @return void
     */
    public function testGetShopPage()
    {
        $response = $this->get('/shop');
        $response->assertStatus(200);
    }

    /**
 * A test route get catalog by id.
 *
 * @return void
 */
    public function testGetCatalogByID()
    {
        $response = $this->get('/shop/category/1');
        $response->assertStatus(200);
    }

    /**
     * A test get product by id page.
     *
     * @return void
     */
    public function testGetProductByIdPage()
    {
        $this->withoutExceptionHandling();
        $response = $this->get('/shop/1');
        $response->assertStatus(200);
    }

    /**
     * A test cart page.
     *
     * @return void
     */
    public function testGetCartPage()
    {
        $this->withoutExceptionHandling();

        $response = $this->actingAs($this->user)->get('/cart');
        
        $response->assertStatus(200);
    }

    /**
     * A test Checkout Success page.
     *
     * @return void
     */
    public function testGetCheckoutSuccessPage()
    {
        $response = $this->get('/checkout/success');
        $response->assertStatus(200);
    }

    /**
     * A test Orders page.
     *
     * @return void
     */
    public function testGetOrdersPage()
    {
        $response = $this->actingAs($this->user)
            ->get('/orders');

        $response->assertStatus(200);
    }

    /**
     * A test get Order page by id.
     *
     * @return void
     */
    public function testGetOrderByIdPage()
    {
        $response = $this->actingAs($this->user)
            ->get('/order/1');

        $response->assertStatus(200);
    }
}
