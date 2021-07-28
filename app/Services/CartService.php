<?php

namespace App\Services;

use App\OrderData;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Product;

class CartService
{
    private Product $product;
    private Request $request;
    private OrderData $orderData;
    
    public function __construct (Product $product, Request $request, OrderData $orderData)
    {
        $this->product = $product;
        $this->request = $request;
        $this->orderData = $orderData;
    }
    
    /**
     * @param int $productId
     * @param int $qty
     * @return mixed
     */
    public function addToCart(int $productId, int $qty)
    {
        $cartProducts = $this->request->session()->get('cartProducts');
        $total = 0;
        //If product exist in the cart
        if (!empty($cartProducts)) {
            $total = $cartProducts['total'];
            if (array_key_exists($productId, $cartProducts)) {
                $total += $this->product->getProductPriceById($productId) * $qty;
                $cartProducts['total'] = $total;
                $qty += $cartProducts[$productId]['productQty'];
                $cartProducts[$productId] = ['productQty' => $qty, 'isRelatedProduct' => $cartProducts[$productId]['isRelatedProduct']];
                $this->request->session()->forget('cartProducts');
                $this->request->session()->put('cartProducts', $cartProducts);
                if ($this->request->ajax()) return ['items' => (count($cartProducts) - 1), 'total' => $cartProducts['total']];
                return redirect('cart');
            }
        }

        $total += $this->product->getProductPriceById($productId) * $qty;
        $this->request->session()->put('cartProducts.total', $total);
        $this->request->session()->put('cartProducts.shippingMethodId', '');
        $this->request->session()->put('cartProducts.' . $productId,
            ['productQty' => $qty, 'isRelatedProduct' => 0]
        );
        //Todo return ok
        return true;
    }
    
    /**
     * @param int $productId
     * @param int $qty
     * @return bool
     */
    public function updateQty(int $productId, int $qty): bool
    {
        $cartProducts = $this->request->session()->get('cartProducts');
        if (!$cartProducts || !array_key_exists($productId, $cartProducts)) {
            return false;
        }
        
        $oldQty = $cartProducts[$productId]['productQty'];
        $price = $this->product->getProductPriceById($productId);
        $diff = $price * ($qty - $oldQty);
        $cartProducts['total'] = $cartProducts['total'] + $diff;
        $cartProducts[$productId] = ['productQty' => $qty, 'isRelatedProduct' => $cartProducts[$productId]['isRelatedProduct']];
        $this->request->session()->forget('cartProducts');
        $this->request->session()->put('cartProducts', $cartProducts);
        
        return true;
    }
    
    /**
     * @param int $orderId
     */
    public function makeCartByOrderId(int $orderId): void
    {
        /** @var Collection<OrderData> $orderDetails */
        $orderDetails = $this->orderData->byOrderId($orderId)->get();
        
        if ($orderDetails->isEmpty()) {
            throw (new ModelNotFoundException())->setModel(OrderData::class);
        }
    
        foreach ($orderDetails as $orderRow) {
            $this->addToCart($orderRow->product_id, $orderRow->qty);
        }
    }
}
