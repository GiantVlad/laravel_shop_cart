<?php

namespace Tests\Feature;

use App\OrderData;
use App\Services\Order\OrderStatuses;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Order;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp () :void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }
    
    public function testList()
    {
        Order::factory()->count(5)->create([
            'user_id' => $this->user->id,
        ]);
    
        Order::factory()->count(2)->create();
        
        $response = $this->actingAs($this->user)->get('/orders');
        $response->assertSuccessful();
        $response->assertViewHas('orders');
        $response->assertViewIs('shop.orders');
        $data = $response->viewData('orders')->resolve()['data'];
        $this->assertCount(5, $data);
        $item = $data->first()->resolve();
        $this->assertArrayHasKey('id', $item);
        $this->assertArrayHasKey('total', $item);
        $this->assertArrayHasKey('status', $item);
        $this->assertArrayHasKey('label', $item);
        $this->assertArrayHasKey('created_at', $item);
        $this->assertArrayHasKey('uri', $item);
    }
    
    public function testListUserNotAuth()
    {
        $response = $this->get('/orders');
        $response->assertRedirect(route('login'));
    }
    
    public function testDoActionUndo()
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
        ]);
        $response = $this->actingAs($this->user)->post('/order/action', ['id' => $order->id, 'action' => 'undo']);
        $response->assertSuccessful();
        $response->assertJsonStructure(['status']);
        $data = $response->decodeResponseJson();
        $this->assertEquals(OrderStatuses::DELETED, $data['status']);
    }
    
    /**
     * @throws \Throwable
     */
    public function testDoActionRepeatOrder()
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => OrderStatuses::DELETED,
        ]);
        
        OrderData::factory()->create([
            'order_id' => $order->id,
        ]);
        $response = $this->actingAs($this->user)
            ->post('/order/action', ['id' => $order->id, 'action' => 'repeat']);
        $response->assertSuccessful();
        $data = $response->decodeResponseJson();
        $this->assertEquals('"redirect_to_cart"' , $data->json);
    }
    
    public function testDoActionNotUsersOrder()
    {
        $order = Order::factory()->create();
        
        $response = $this->actingAs($this->user)
            ->withHeader('Accept', 'application/json')
            ->post('/order/action', ['id' => $order->id, 'action' => 'undo']);
        
        $response->assertStatus(422);
    }
}
