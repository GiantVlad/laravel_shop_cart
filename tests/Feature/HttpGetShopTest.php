<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Dispatch;
use App\Payment;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;
use App\User;
use App\Order;
use App\Catalog;
use App\Product;

class HttpGetShopTest extends TestCase
{
    private User $user;
    private Order $order;
    private Collection $products;

    public function setUp () :void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        Catalog::factory()->count(10)->create();

        $this->products = Product::factory()->count(20)->create();
    
        $this->order = Order::factory()
            ->has(Payment::factory()->count(1))
            ->has(Dispatch::factory()->count(1))
            ->create([
                'user_id' => $this->user->id,
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
        $response->assertSuccessful();
    }

    /**
     * A test shop page.
     *
     * @return void
     */
    public function testGetShopPage()
    {
        $response = $this->get('/shop');
        $response->assertSuccessful();
    }
    
    /**
     * A test get product by id page.
     *
     * @return void
     */
    public function testGetProductByIdPage()
    {
        $response = $this->get('/shop/' . $this->products->first()->id);
        $response->assertSuccessful();
    }

    /**
     * A test route get catalog by id.
     *
     * @return void
     */
    public function testGetCatalogByID()
    {
        $response = $this->get('/shop/category/1');
        $response->assertSuccessful();
    }

    /**
     * A test Checkout Success page.
     *
     * @return void
     */
    public function testGetCheckoutSuccessPage()
    {
        $response = $this->get('/checkout/success');
        $response->assertSuccessful();
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

        $response->assertSuccessful();
    }

    /**
     * A test get Order page by id.
     *
     * @return void
     */
    public function testGetOrderByIdPage()
    {
        $this->actingAs($this->user);
        $response = $this->get('/order/' . $this->order->id);

        $response->assertSuccessful();
    }
}
