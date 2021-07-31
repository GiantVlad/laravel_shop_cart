<?php

declare(strict_types=1);

namespace App\Services\Cart;

use App\OrderData;
use App\Product;
use App\RelatedProduct;
use Illuminate\Contracts\Session\Session;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartService
{
    private Session $session;
    private Product $product;
    private RelatedProduct $relatedProduct;
    private OrderData $orderData;
    
    public function __construct(
        Session $session,
        Product $product,
        RelatedProduct $relatedProduct,
        OrderData $orderData
    ) {
        $this->session = $session;
        $this->product = $product;
        $this->relatedProduct = $relatedProduct;
        $this->orderData = $orderData;
    }
    
    /**
     * @param int $productId
     */
    public function addRelatedProduct(int $productId): void
    {
        //add to session
        $this->session->put('cartProducts.' . $productId,
            ['productQty' => 1, 'isRelatedProduct' => 1]
        );
        
        $price = $this->product->getProductPriceById($productId);
        $total = $this->session->get('cartProducts.total') + $price;
        $this->session->put('cartProducts.total', $total);
        
        $cartProducts = session('cartProducts');
        $productsIds = [];
        foreach ($cartProducts as $key => $cartProduct) {
            if ($key === 'total' || $key === 'shippingMethodId') {
                continue;
            }
            $productsIds[] = $key;
        }
        
        $this->relatedProduct->getRelatedProduct($productsIds);
    }
    
    /**
     * @param int $productId
     * @param int $qty
     * @return mixed
     */
    public function addToCart(int $productId, int $qty)
    {
        $cartProducts = $this->session->get('cartProducts');
        $total = 0;
        //If product exist in the cart
        if (!empty($cartProducts)) {
            $total = $cartProducts['total'];
            if (array_key_exists($productId, $cartProducts)) {
                $total += $this->product->getProductPriceById($productId) * $qty;
                $cartProducts['total'] = $total;
                $qty += $cartProducts[$productId]['productQty'];
                $cartProducts[$productId] = ['productQty' => $qty, 'isRelatedProduct' => $cartProducts[$productId]['isRelatedProduct']];
                $this->session->forget('cartProducts');
                $this->session->put('cartProducts', $cartProducts);
                
//                if ($this->request->ajax()) {
//                    return ['items' => (count($cartProducts) - 1), 'total' => $cartProducts['total']];
//                }
//
//                return redirect('cart');
            }
        }
        
        $total += $this->product->getProductPriceById($productId) * $qty;
        $this->session->put('cartProducts.total', $total);
        $this->session->put('cartProducts.shippingMethodId', '');
        $this->session->put('cartProducts.' . $productId,
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
        $cartProducts = $this->session->get('cartProducts');
        if (!$cartProducts || !array_key_exists($productId, $cartProducts)) {
            return false;
        }
        
        $oldQty = $cartProducts[$productId]['productQty'];
        $price = $this->product->getProductPriceById($productId);
        $diff = $price * ($qty - $oldQty);
        $cartProducts['total'] = $cartProducts['total'] + $diff;
        $cartProducts[$productId] = ['productQty' => $qty, 'isRelatedProduct' => $cartProducts[$productId]['isRelatedProduct']];
        $this->session->forget('cartProducts');
        $this->session->put('cartProducts', $cartProducts);
        
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
            $this->addToCart((int)$orderRow->product_id, (int)$orderRow->qty);
        }
    }
}
