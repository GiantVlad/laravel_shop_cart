<?php
/**
 * Created by PhpStorm.
 * User: ale
 * Date: 29.01.2018
 * Time: 18:14
 */

namespace App\Library\Services;

use Illuminate\Http\Request;
use App\Product;

class CartService
{
    private $product;
    private $request;

    public function __construct (Product $product, Request $request)
    {
        $this->product = $product;
        $this->request = $request;
    }

    public function addToCart(int $productId, $qty)
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

    public function updateQty(int $productId, $qty)
    {
        $cartProducts = $this->request->session()->get('cartProducts');
        if (!empty($cartProducts)) {
            if (array_key_exists($productId, $cartProducts)) {
                $oldQty = $cartProducts[$productId]['productQty'];
                $price = $this->product->getProductPriceById($productId);
                $diff = $price * ($qty - $oldQty);
                $cartProducts['total'] = $cartProducts['total'] + $diff;
                $cartProducts[$productId] = ['productQty' => $qty, 'isRelatedProduct' => $cartProducts[$productId]['isRelatedProduct']];
                $this->request->session()->forget('cartProducts');
                $this->request->session()->put('cartProducts', $cartProducts);
                return true;
            }
        }
    }
}