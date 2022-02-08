<?php

declare(strict_types=1);

namespace App\Services\Cart;

use App\OrderData;
use App\Product;
use App\RelatedProduct;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\SimpleCache\InvalidArgumentException;

class CartService
{
    public const CART_KEY = 'cart:user:';
    private const CART_TTL = 7776000; // 3 months
    
    public function __construct(
        private Product $product,
        private RelatedProduct $relatedProduct,
        private OrderData $orderData,
        private Cache $cacheRepository
    ) {}
    
    /**
     * @param int $userId
     * @param int $productId
     *
     * @throws InvalidArgumentException
     */
    public function addRelatedProduct(int $userId, int $productId): void
    {
        $cartProducts = $this->getCart($userId) ?? [];
        $cartProducts[$productId] = ['productQty' => 1, 'isRelatedProduct' => 1];
        
        $price = $this->product->getProductPriceById($productId);
        $cartProducts['total'] = ($cartProducts['total'] ?? 0) + $price;
        $this->storeCart($userId, $cartProducts);
        
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
     * @param int $userId
     * @param int $productId
     * @param int $qty
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function addToCart(int $userId, int $productId, int $qty): bool
    {
        $cartProducts = $this->getCart($userId) ?? [];
        $total = 0;
        //If product exist in the cart
        if (!empty($cartProducts)) {
            $total = $cartProducts['total'];
            if (array_key_exists($productId, $cartProducts)) {
                $total += $this->product->getProductPriceById($productId) * $qty;
                $cartProducts['total'] = $total;
                $qty += $cartProducts[$productId]['productQty'];
                $cartProducts[$productId] = [
                    'productQty' => $qty,
                    'isRelatedProduct' => $cartProducts[$productId]['isRelatedProduct']
                ];
                
                $this->storeCart($userId, $cartProducts);
    
                return true;
            }
        }
        $total += $this->product->getProductPriceById($productId) * $qty;
        $cartProducts[$productId] = [
            'productQty' => $qty,
            'isRelatedProduct' => 0,
        ];
        $cartProducts['total'] = $total;
        $cartProducts['shippingMethodId'] = null;
        
        $this->storeCart($userId, $cartProducts);
       
        return true;
    }
    
    /**
     * @param int $userId
     * @param int $productId
     * @param int $qty
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function updateQty(int $userId, int $productId, int $qty): bool
    {
        $cartProducts = $this->getCart($userId);
        if (!$cartProducts || !array_key_exists($productId, $cartProducts)) {
            return false;
        }
        
        $oldQty = $cartProducts[$productId]['productQty'];
        $price = $this->product->getProductPriceById($productId);
        $diff = $price * ($qty - $oldQty);
        $cartProducts['total'] = $cartProducts['total'] + $diff;
        $cartProducts[$productId] = ['productQty' => $qty, 'isRelatedProduct' => $cartProducts[$productId]['isRelatedProduct']];
        
        $this->storeCart($userId, $cartProducts);
        
        return true;
    }
    
    /**
     * @param int $userId
     * @param int $orderId
     *
     * @throws InvalidArgumentException
     */
    public function makeCartByOrderId(int $userId, int $orderId): void
    {
        /** @var Collection<OrderData> $orderDetails */
        $orderDetails = $this->orderData->byOrderId($orderId)->get();
        
        if ($orderDetails->isEmpty()) {
            throw (new ModelNotFoundException())->setModel(OrderData::class);
        }
        
        foreach ($orderDetails as $orderRow) {
            $this->addToCart($userId, (int)$orderRow->product_id, (int)$orderRow->qty);
        }
    }
    
    /**
     * @param int $userId
     * @return array|null
     *
     * @throws InvalidArgumentException
     */
    public function getCart(int $userId): ?array
    {
        return $this->cacheRepository->get(self::CART_KEY . $userId);
    }
    
    /**
     * @param int $userId
     * @param array $cart
     *
     * @return void
     */
    public function storeCart(int $userId, array $cart): void
    {
        $this->cacheRepository->put(self::CART_KEY . $userId, $cart, self::CART_TTL);
    }
    
    /**
     * @param int $userId
     *
     * @return void
     */
    public function forget(int $userId): void
    {
        $this->cacheRepository->forget(self::CART_KEY . $userId);
    }
}
