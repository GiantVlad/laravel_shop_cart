<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CartAddRelatedRequest;
use App\Http\Requests\CartRequest;
use App\Http\Resources\CartPostResource;
use App\Product;
use App\RelatedProduct;
use App\Services\Cart\CartPostActions;
use App\Services\Cart\CartPostDTO;
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
    private Product $product;
    private RelatedProduct $relatedProduct;
    private CartService $cartService;

    public function __construct(
        Product $product,
        RelatedProduct $relatedProduct,
        CartService $cartService,
        
    ) {
        $this->product = $product;
        $this->relatedProduct = $relatedProduct;
        $this->cartService = $cartService;

        $this->middleware('auth')->except('logout');
    }

    // ToDo return one type instead of array or string
    
    /**
     * @param CartRequest $request
     * @return array|int|string|JsonResource
     */
    public function post(CartRequest $request): array|int|string|JsonResource
    {
        $data = $request->validated();
        $cartProducts = $request->session()->get('cartProducts');
        if (is_null($cartProducts)) {
            return 0;
        }
        
        if ($data['input'] === CartPostActions::REMOVE_ROW) { //Product has been removed (ajax)
            $productId = $request->get('productId');

            if ($request->get('isRelated') > 0) {
                $this->relatedProduct->where('id', $productId)->increment('points', -3);
            }
            
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
        } elseif ($data['input'] === CartPostActions::CHANGE_SHIPPING) {
            if (array_key_exists('total', $cartProducts)) {
                $cartProducts['total'] = $request->subtotal;
                $cartProducts['shippingMethodId'] = $request->shippingMethodId;
                $request->session()->forget('cartProducts');
                $request->session()->put('cartProducts', $cartProducts);
            }
        }
    
        $dto = new CartPostDTO((count($cartProducts) - 2), $cartProducts['total']);
    
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
}
