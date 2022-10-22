<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Dispatch;
use App\OrderData;
use App\Payment;
use App\Services\Order\OrderStatuses;
use App\Services\Payment\PaymentMethodManager;
use App\Services\Payment\PaymentResponse;
use Tests\TestCase;
use App\User;
use App\Order;

class OrderControllerTest extends TestCase
{
    private User $user;

    public function setUp() :void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }
    
    public function testList(): void
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

    public function testListUserNotAuth(): void
    {
        $response = $this->get('/orders');
        $response->assertRedirect(route('login'));
    }

    public function testDoActionUndo(): void
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
    
    public function testDoActionRePay(): void
    {
        $paymentResponse = $this->createMock(PaymentResponse::class);
        $paymentResponse->expects($this->exactly(2))
            ->method('getCheckoutUrl')
            ->willReturn('some_url');
        
        $paymentService = $this->createMock(PaymentMethodManager::class);
        $paymentService->expects($this->once())->method('pay')->willReturn($paymentResponse);
        
        app()->instance(PaymentMethodManager::class, $paymentService);
        
        /** @var Order $order */
        $order = Order::factory()
            ->has(Payment::factory()->count(1))
            ->has(Dispatch::factory()->count(1))
            ->create([
                'user_id' => $this->user->id,
            ]);
        $response = $this->actingAs($this->user)
            ->post('/order/action', ['id' => $order->id, 'action' => 're_payment']);
        $response->assertSuccessful();
        $response->assertJsonStructure(['redirect_to']);
        $data = $response->decodeResponseJson();
        $this->assertEquals('some_url', $data['redirect_to']);
    }
    
    /**
     * @throws \Throwable
     */
    public function testDoActionRepeatOrder(): void
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
        $response->assertJsonStructure(['redirect_to']);
        $data = $response->decodeResponseJson();
        $this->assertEquals(route('get.cart'), $data['redirect_to']);
    }

    public function testDoActionOrderIsNotOfUser(): void
    {
        $order = Order::factory()->create();

        $response = $this->actingAs($this->user)
            ->withHeader('Accept', 'application/json')
            ->post('/order/action', ['id' => $order->id, 'action' => 'undo']);

        $response->assertStatus(422);
    }
}
