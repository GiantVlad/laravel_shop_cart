<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Product;
use App\RelatedProduct;
use App\Services\Cart\CartPostActions;
use App\ShippingMethod;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class CartController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private Product $product;
    private RelatedProduct $relatedProduct;
    private CartService $cartService;

    public function __construct(
        Product $product,
        RelatedProduct $relatedProduct,
        CartService $cartService
    ) {
        $this->product = $product;
        $this->relatedProduct = $relatedProduct;
        $this->cartService = $cartService;

        $this->middleware('auth')->except('logout');
    }

    // ToDo return one type instead of View, array or string
    
    /**
     * @param CartRequest $request
     * @return array|View|int|string
     */
    public function post(CartRequest $request): array|View|int|string
    {
        $data = $request->validated();
        if ($data['input'] === CartPostActions::EMPTY_CART) { //All products has been removed (ajax)
            return view('empty-cart');
        } elseif ($data['input'] === CartPostActions::REMOVE_ROW) { //Product has been removed (ajax)
            $productId = $request->get('productId');

            if ($request->get('isRelated') > 0) {
                $this->relatedProduct->where('id', $productId)->increment('points', -3);
            }

            if ($request->session()->has('cartProducts')) {
                $cartProducts = $request->session()->get('cartProducts');

                if (array_key_exists($productId, $cartProducts)) {
                    unset($cartProducts[$productId]);
                    if (count($cartProducts) <= 2) {
                        $request->session()->forget('cartProducts');
                        
                        return ['items' => 0, 'total' => 0];
                    }
                    $cartProducts['total'] = $request->get('subtotal');
                    $request->session()->forget('cartProducts');
                    $request->session()->put('cartProducts', $cartProducts);
                }
                
                return ['items' => (count($cartProducts) - 2), 'total' => $cartProducts['total']];
            }
            
            return 0;
        } elseif ($data['input'] === CartPostActions::CHANGE_SHIPPING) {
            if ($request->session()->has('cartProducts')) {
                $cartProducts = $request->session()->get('cartProducts');

                if (array_key_exists('total', $cartProducts)) {
                    $cartProducts['total'] = $request->subtotal;
                    $cartProducts['shippingMethodId'] = $request->shippingMethodId;
                    $request->session()->forget('cartProducts');
                    $request->session()->put('cartProducts', $cartProducts);
                }

                return ['items' => (count($cartProducts) - 2), 'total' => $cartProducts['total']];
            }
        } elseif ($data['input'] === CartPostActions::ADD_RELATED) {
            $this->addRelatedProduct($request);
        }
        
        return 'ok';
    }
    
    /**
     * @param ShippingMethod $shippingMethod
     * @return View
     */
    public function index(ShippingMethod $shippingMethod): View
    {
        $cartProducts = session('cartProducts');
        
        if (empty($cartProducts)) {
            return view('empty-cart');
        }

        $products = [];
        $index = 0;
        $productsIds = [];

        $shippingMethods = $shippingMethod->getAllEnabled();
        
        foreach ($cartProducts as $key => $cartProduct) {
            if ($key === 'total') {
                continue;
            }
            if ($key === 'shippingMethodId') {
                $shippingMethods->map(function($method) use ($cartProducts) {
                    if ($method->id == $cartProducts['shippingMethodId']) {
                        $method['selected'] = true;
                    } else {
                        $method['selected'] = false;
                    }
                    return $method;
                });

                continue;
            }
            $products[$index] = $this->product->findOrFail($key);
            $products[$index]->is_related = $cartProduct['isRelatedProduct'];
            $products[$index]->qty = $cartProduct['productQty'];
            $productsIds[] = $products[$index]->id;
            $index++;
        }
        
        $relatedProduct = $this->relatedProduct->getRelatedProduct($productsIds);
        
        return view(
            'cart',
            ['products' => $products, 'relatedProduct' => $relatedProduct, 'shippingMethods' => $shippingMethods]
        );
    }
    
    /**
     * @param Request $request
     * @return array|RedirectResponse|Redirector
     */
    public function addToCart(Request $request): array|RedirectResponse|Redirector
    {
        $request->validate(
            [
                'productId' => 'required|integer|min:1|max:99999',
                'productQty' => 'required|integer|min:1|max:99'
            ],
            [
                'productQty.required' => 'The Quantity field can not be blank.',
                'productQty.integer' => 'Quantity must be integer.',
                'productQty.min' => 'Minimum of Quantity is 1 psc.',
                'productQty.max' => 'Maximum of Quantity is 99 psc.'
            ]);

        $productId = $request->get('productId');
        $qty = $request->get('productQty');

        if ($request->get('updateQty')) {
            $this->cartService->updateQty($productId, $qty);
        } else {
            $this->cartService->addToCart($productId, $qty);
        }

        if ($request->ajax()) {
            return [
                'items' => (count($request->session()->get('cartProducts')) - 2),
                'total' => $request->session()->get('cartProducts.total')
            ];
        }

        return redirect('cart');
    }
    
    /**
     * @param Request $request
     * @return Product|null
     */
    private function addRelatedProduct(Request $request): ?Product
    {
        //add to session
        $request->session()->put('cartProducts.' . $request->related_product_id,
            ['productQty' => 1, 'isRelatedProduct' => 1]
        );

        $price = $this->product->getProductPriceById($request->related_product_id);
        $total = $request->session()->get('cartProducts.total') + $price;
        $request->session()->put('cartProducts.total', $total);

        $cartProducts = session('cartProducts');
        $productsIds = [];
        foreach ($cartProducts as $key => $cartProduct) {
            if ($key === 'total' || $key === 'shippingMethodId') continue;
            $productsIds[] = $key;
        }
    
        return $this->relatedProduct->getRelatedProduct($productsIds);
    }
}
