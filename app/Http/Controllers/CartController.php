<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CartAddRelatedRequest;
use App\Http\Requests\CartAddRequest;
use App\Http\Requests\CartRemoveItemRequest;
use App\Http\Requests\CartChangeShippingRequest;
use App\Http\Resources\CartPostResource;
use App\Product;
use App\RelatedProduct;
use App\Services\Cart\CartPostDTO;
use App\Services\Recommended\Recommended;
use App\ShippingMethod;
use App\Services\Cart\CartService;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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
        private Recommended $recommendedService,
        private Cache $cacheRepository,
    ) {
        $this->middleware('auth')->except('logout');
    }
    
    /**
     * @param CartChangeShippingRequest $request
     *
     * @return JsonResource
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function changeShipping(CartChangeShippingRequest $request): JsonResource
    {
        $input = $request->validated();
        $cartProducts = $this->cartService->getCart($request->user()->id);
        if (is_null($cartProducts)) {
            return new CartPostResource(new CartPostDTO(0, 0));
        }
        
        if (array_key_exists('total', $cartProducts)) {
            $cartProducts['total'] = (float)$input['subtotal'];
            $cartProducts['shippingMethodId'] = (int)$input['shippingMethodId'];
            $this->cartService->storeCart($request->user()->id, $cartProducts);
        }
    
        $dto = new CartPostDTO((count($cartProducts) - 2), $cartProducts['total']);
    
        return new CartPostResource($dto);
    }
    
    /**
     * @param CartRemoveItemRequest $request
     *
     * @return JsonResource
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function removeItem(CartRemoveItemRequest $request): JsonResource
    {
        $cartProducts = $this->cartService->getCart($request->user()->id);
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
            $this->cartService->forget($request->user()->id);
            if (count($cartProducts) > 2) {
                $cartProducts['total'] = $subtotal;
                $itemsCount = count($cartProducts) - 2;
                $this->cartService->storeCart($request->user()->id, $cartProducts);
            }
        }
    
        $dto = new CartPostDTO($itemsCount, $cartProducts['total']);
    
        return new CartPostResource($dto);
    }
    
    /**
     * @param Request $request
     *
     * @return CartPostResource
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function cartContent(Request $request): CartPostResource
    {
        $cart = $this->cacheRepository->get(CartService::CART_KEY . $request->user()->id) ?? [];
        $dto = new CartPostDTO(max(count($cart) - 2, 0), $cart['total'] ?? 0);
    
        return new CartPostResource($dto);
    }
    
    /**
     * @param Request $request
     * @param ShippingMethod $shippingMethod
     *
     * @return View
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function index(Request $request, ShippingMethod $shippingMethod): View
    {
        $cartProducts = $this->cacheRepository->get(CartService::CART_KEY . $request->user()->id);
        
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
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function addRelated(CartAddRelatedRequest $request): JsonResponse
    {
        $id = (int)$request->validated()['id'];
        $this->cartService->addRelatedProduct($request->user()->id, $id);
        
        return response()->json();
    }
    
    /**
     * @param CartAddRequest $request
     * @return array|RedirectResponse|Redirector
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function addToCart(CartAddRequest $request): array|RedirectResponse|Redirector
    {
        $productId = (int)$request->get('productId');
        $qty = (int)$request->get('productQty');
        $userId = $request->user()->id;
        if ((int)$request->get('updateQty')) {
            $this->cartService->updateQty($userId, $productId, $qty);
        } else {
            $this->cartService->addToCart($userId, $productId, $qty);
        }
        
        $cart = $this->cartService->getCart($userId);

        if ($request->ajax()) {
            return [
                'items' => max((count($cart) - 2), 0),
                'total' => $cart['total'] ?? 0,
            ];
        }

        return redirect('cart');
    }
}
