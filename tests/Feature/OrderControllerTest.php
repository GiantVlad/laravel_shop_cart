<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Order;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp () :void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        Order::factory()->count(5)->create([
            'user_id' => $this->user->id,
        ]);
        
        Order::factory()->count(2)->create();
    }
    
    public function testList()
    {
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
    }
    
    public function testListUserNotAuth()
    {
        $response = $this->get('/orders');
        $response->assertRedirect(route('login'));
    }
}
