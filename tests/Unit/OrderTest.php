<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Order;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private Order $order;
    public function setUp(): void
    {
        parent::setUp();
        $this->order = new Order;
        $this->order->order_label = 'ips_77889900';
        $this->order->commentary = 'commentary';
        $this->order->total = 152;
        $this->order->user_id = 2;
        $this->order->save();
    }
    
    public function testGetOrderByLabel(): void
    {
        $orderLabel = $this->order->getOrderByLabel('s_7788')->first()->order_label;
        $this->assertSame('ips_77889900', $orderLabel);
    }

    public function testGetOrdersByUserId(): void
    {
        $this->order = new Order;
        $this->order->order_label = 'ips_77889901';
        $this->order->commentary = 'commentary1';
        $this->order->total = 152;
        $this->order->user_id = 3;
        $this->order->save();

        $this->order = new Order;
        $this->order->order_label = 'ips_77889902';
        $this->order->commentary = 'commentary2';
        $this->order->total = 152;
        $this->order->user_id = 3;
        $this->order->save();

        $ordersCount = $this->order->getOrdersByUserId(3)->get()->count();
        $this->assertEquals(2, $ordersCount);
    }
}
