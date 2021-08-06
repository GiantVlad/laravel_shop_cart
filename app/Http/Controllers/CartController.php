<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CartAddRelatedRequest;
use App\Http\Requests\CartRemoveItemRequest;
use App\Http\Requests\CartChangeShippingRequest;
use App\Http\Resources\CartPostResource;
use App\Product;
use App\RelatedProduct;
use App\Services\Cart\CartPostDTO;
use App\Services\Recommended\Recommended;
use App\ShippingMethod;
use App\Services\Cart\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Redirector;

class CartController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct(
        private Product $product,
        private RelatedProduct $relatedProduct,
        private CartService $cartService,
        private Recommended $recommendedService
    ) {
        $this->middleware('auth')->except('logout');
    }

    /**
     * @param CartChangeShippingRequest $request
     * @return JsonResource
     */
    public function changeShipping(CartChangeShippingRequest $request): JsonResource
    {
        $input = $request->validated();
        $cartProducts = $request->session()->get('cartProducts');
        if (is_null($cartProducts)) {
            return new CartPostResource(new CartPostDTO(0, 0));
        }
        
        if (array_key_exists('total', $cartProducts)) {
            $cartProducts['total'] = (float)$input['subtotal'];
            $cartProducts['shippingMethodId'] = (int)$input['shippingMethodId'];
            $request->session()->forget('cartProducts');
            $request->session()->put('cartProducts', $cartProducts);
        }
    
        $dto = new CartPostDTO((count($cartProducts) - 2), $cartProducts['total']);
    
        return new CartPostResource($dto);
    }
    
    /**
     * @param CartRemoveItemRequest $request
     * @return JsonResource
     */
    public function removeItem(CartRemoveItemRequest $request): JsonResource
    {
        $cartProducts = $request->session()->get('cartProducts');
        if (is_null($cartProducts)) {
            return new CartPostResource(new CartPostDTO(0, 0));
        }
    
        $input = $request->validated();
        $productId = (int)$input['productId'];
        $isRelated = (bool)$input['isRelated'];
        $subtotal = (float)$input['subtotal'];
    
        if ($isRelated) {
            $this->recommendedService->incrementRate($productId, Recommended::RATE_IMPACT_AFTER_REMOVAL_FROM_CART);
        }
        
        $itemsCount = 0;
        
        if (array_key_exists($productId, $cartProducts)) {
            unset($cartProducts[$productId]);
            $cartProducts['total'] = 0;
            $request->session()->forget('cartProducts');
            if (count($cartProducts) > 2) {
                $cartProducts['total'] = $subtotal;
                $itemsCount = count($cartProducts) - 2;
                $request->session()->put('cartProducts', $cartProducts);
            }
        }
    
        $dto = new CartPostDTO($itemsCount, $cartProducts['total']);
    
        return new CartPostResource($dto);
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
     * @param CartAddRelatedRequest $request
     * @return JsonResponse
     */
    public function addRelated(CartAddRelatedRequest $request): JsonResponse
    {
        $id = (int)$request->validated()['id'];
        $this->cartService->addRelatedProduct($id);
        
        return response()->json();
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

        $productId = (int)$request->get('productId');
        $qty = (int)$request->get('productQty');

        if ((int)$request->get('updateQty')) {
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
}
