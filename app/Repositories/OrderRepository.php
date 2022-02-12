<?php

namespace App\Repositories;

use App\Order;
use App\OrderData;
use App\Product;
use App\RelatedProduct;
use App\User;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\ConnectionInterface;

class OrderRepository
{
    public function __construct(
        private Product $mProduct,
        private RelatedProduct $mRelatedProduct,
        private Repository $config,
        private ConnectionInterface $dbConnection,
    ) {}
    
    /**
     * @param User $user
     * @param array $requestData
     *
     * @return Order
     * @throws \Throwable
     */
    public function createOrder(User $user, array $requestData): Order
    {
        $orderLabel = $this->config->get('app.order_prefix') . '_' . time();
        $order = new Order();
        $this->dbConnection->transaction(function() use ($orderLabel, $requestData, $user, $order) {
            $order->commentary = '';
            $order->total = $requestData['subtotal'];
            $order->status = 'pending payment';
            $order->order_label = $orderLabel;
            $order->user_id = $user->id;
            $order->save();
    
            $products = $requestData['product_ids'];
            $orderData = [];
            $relatedProductIds = [];
            for ($i = 0; $i < count($products); $i++) {
                /** @var Product $product */
                $product = $this->mProduct->find($products[$i]);
                $price = $product->price;
                $orderData[] = new OrderData([
                    'order_id' => $order->id,
                    'product_id' => $products[$i],
                    'is_related_product' => $requestData['isRelatedProduct'][$i],
                    'price' => $price,
                    'qty' => $requestData['productQty'][$i],
                ]);
    
                if ($requestData['isRelatedProduct'][$i] == 1) {
                    $relatedProductIds[] = (int)$requestData['isRelatedProduct'][$i];
                }
            }
            $this->mRelatedProduct->whereIn('id', $relatedProductIds)->increment('points', 5);
            $order->orderData()->saveMany($orderData);
        });
        
        return $order;
    }
}
