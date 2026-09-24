<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CartAddRelatedRequest;
use App\Http\Requests\CartAddRequest;
use App\Http\Requests\CartChangePaymentRequest;
use App\Http\Requests\CartRemoveItemRequest;
use App\Http\Requests\CartChangeShippingRequest;
use App\Http\Resources\CartPostResource;
use App\Product;
use App\PaymentMethod;
use App\RelatedProduct;
use App\Services\Cart\CartPostDTO;
use App\Services\Payment\PaymentMethodManager;
use App\Services\Recommended\Recommended;
use App\ShippingMethod;
use App\Services\Cart\CartService;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Validation\ValidationException;
use Psr\SimpleCache\InvalidArgumentException;

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
        private PaymentMethodManager $paymentMethodList,
    ) {
        $this->middleware('auth')->except('logout');
    }
    
    /**
     * @param CartChangeShippingRequest $request
     *
     * @return JsonResource
     * @phpstan-ignore-next-line
     * @throws InvalidArgumentException
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

            // Payment options depend on the shipping method, so a payment method the
            // new shipping method does not offer has to be re-chosen by the customer.
            $selectedPaymentId = (int)($cartProducts['paymentMethodId'] ?? 0);
            if ($selectedPaymentId > 0
                && ! $this->paymentMethodList->isAvailableForShippingMethod($selectedPaymentId, (int)$input['shippingMethodId'])
            ) {
                $cartProducts['paymentMethodId'] = null;
            }

            $this->cartService->storeCart($request->user()->id, $cartProducts);
        }
    
        return $this->getCartPostResource($cartProducts);
    }
    
    /**
     * Payment methods the given shipping method accepts, for the cart's payment selector.
     *
     * @param int $shippingMethodId
     * @param ShippingMethod $shippingMethod
     * @param PaymentMethodManager $paymentMethodList
     * @return JsonResponse
     */
    public function paymentMethods(
        int $shippingMethodId,
        ShippingMethod $shippingMethod,
        PaymentMethodManager $paymentMethodList
    ): JsonResponse {
        $shippingMethod->findOrFail($shippingMethodId);

        if (! $shippingMethod->getStatusById($shippingMethodId)) {
            throw ValidationException::withMessages([
                'shippingMethodId' => 'The selected shipping method is disabled.',
            ]);
        }

        $payments = $paymentMethodList->getAllEnabledForShippingMethod($shippingMethodId)
            ->map(static function (PaymentMethod $method): array {
                return [
                    'id' => $method->id,
                    'label' => $method->label,
                    'config_key' => $method->config_key,
                ];
            })
            ->values();

        return response()->json(['payments' => $payments]);
    }
    
    /**
     * @param CartChangePaymentRequest $request
     *
     * @return JsonResource
     * @phpstan-ignore-next-line
     * @throws InvalidArgumentException
     */
    public function changePayment(CartChangePaymentRequest $request): JsonResource
    {
        $input = $request->validated();
        $cartProducts = $this->cartService->getCart($request->user()->id);
        if (is_null($cartProducts)) {
            return new CartPostResource(new CartPostDTO(0, 0));
        }
        
        $shippingMethodId = (int)($cartProducts['shippingMethodId'] ?? 0);
        $paymentMethodId = (int)$input['paymentMethodId'];

        if ($shippingMethodId < 1) {
            throw ValidationException::withMessages([
                'paymentMethodId' => 'Choose a shipping method before choosing how to pay.',
            ]);
        }

        if (! $this->paymentMethodList->isAvailableForShippingMethod($paymentMethodId, $shippingMethodId)) {
            throw ValidationException::withMessages([
                'paymentMethodId' => 'The selected payment method is not available for this shipping method.',
            ]);
        }

        $cartProducts['paymentMethodId'] = $paymentMethodId;
        $this->cartService->storeCart($request->user()->id, $cartProducts);
        
        return $this->getCartPostResource($cartProducts);
    }
    
    /**
     * @param array $cartProducts
     * @return CartPostResource
     */
    private function getCartPostResource(array $cartProducts): CartPostResource
    {
        $dto = new CartPostDTO((count($cartProducts) - 3), $cartProducts['total']);
    
        return new CartPostResource($dto);
    }
    
    /**
     * @param CartRemoveItemRequest $request
     *
     * @return JsonResource
     * @phpstan-ignore-next-line
     * @throws InvalidArgumentException
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
            if (count($cartProducts) > 3) {
                $cartProducts['total'] = $subtotal;
                $itemsCount = count($cartProducts) - 3;
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
     * @phpstan-ignore-next-line
     * @throws InvalidArgumentException
     */
    public function cartContent(Request $request): CartPostResource
    {
        $cart = $this->cacheRepository->get(CartService::CART_KEY . $request->user()->id) ?? [];
        $dto = new CartPostDTO(max(count($cart) - 3, 0), $cart['total'] ?? 0);
    
        return new CartPostResource($dto);
    }
    
    /**
     * @param Request $request
     * @param ShippingMethod $shippingMethod
     * @param PaymentMethodManager $paymentMethodList
     * @return View
     * @throws InvalidArgumentException
     * @phpstan-ignore-next-line
     */
    public function index(
        Request $request,
        ShippingMethod $shippingMethod,
        PaymentMethodManager $paymentMethodList
    ): Response {
        return $this->renderCartPage($request->user()->id, $shippingMethod, $paymentMethodList);
    }
    
    /**
     * @param CartAddRelatedRequest $request
     *
     * @return JsonResponse
     * @phpstan-ignore-next-line
     * @throws InvalidArgumentException
     */
    public function addRelated(CartAddRelatedRequest $request): JsonResponse
    {
        $id = (int)$request->validated()['id'];
        
        $this->cartService->addRelatedProduct($request->user()->id, $id);
        
        return response()->json();
    }
    
    /**
     * @param CartAddRequest $request
     * @return CartPostResource|RedirectResponse|Redirector
     *
     * @phpstan-ignore-next-line
     * @throws InvalidArgumentException
     */
    public function addToCart(
        CartAddRequest $request,
        ShippingMethod $shippingMethod,
        PaymentMethodManager $paymentMethodList,
    ): CartPostResource|RedirectResponse|Redirector|Response
    {
        $productId = (int)$request->get('productId');
        $qty = (int)$request->get('productQty');
        $userId = $request->user()->id;
        if ((int)$request->get('updateQty')) {
            $this->cartService->updateQty($userId, $productId, $qty);
        } else {
            $this->cartService->addToCart($userId, $productId, $qty);
        }
        $cart = $this->cartService->getCart($userId) ?? [];
        if ($request->expectsJson()) {
            $dto = new CartPostDTO(max((count($cart) - 3), 0), ($cart['total'] ?? 0));

            return new CartPostResource($dto);
        }

        if ($request->header('X-Inertia')) {
            return redirect()->route('get.cart');
        }

        return $this->renderCartPage($userId, $shippingMethod, $paymentMethodList);
    }

    private function renderCartPage(
        int $userId,
        ShippingMethod $shippingMethod,
        PaymentMethodManager $paymentMethodList,
    ): Response {
        $cartProducts = $this->cacheRepository->get(CartService::CART_KEY . $userId);

        if (empty($cartProducts)) {
            return Inertia::render(
                'Cart',
                [
                    'products' => [],
                    'relatedProduct' => null,
                    'shippingMethods' => [],
                    'payments' => [],
                ],
            );
        }

        $products = [];
        $index = 0;
        $productsIds = [];

        $shippingMethods = $shippingMethod->getAllEnabled();
        $selectedShippingId = (int)($cartProducts['shippingMethodId'] ?? 0);
        $selectedPaymentId = (int)($cartProducts['paymentMethodId'] ?? 0);

        $shippingMethods->map(static function ($method) use ($selectedShippingId) {
            /** @var ShippingMethod $method */
            $method->selected = $method->id == $selectedShippingId;

            return $method;
        });

        // Nothing to choose from until a shipping method is picked; afterwards the
        // exact payment methods that shipping method accepts.
        $paymentMethods = $selectedShippingId > 0
            ? $paymentMethodList->getAllEnabledForShippingMethod($selectedShippingId)
            : new Collection();

        $paymentMethods->map(static function ($method) use ($selectedPaymentId) {
            /** @var PaymentMethod $method */
            $method->selected = $method->id == $selectedPaymentId;

            return $method;
        });

        foreach ($cartProducts as $key => $cartProduct) {
            if (in_array($key, ['total', 'shippingMethodId', 'paymentMethodId'], true)) {
                continue;
            }

            $product = $this->product->findOrFail($key);

            if ($product instanceof Product) {
                $product->is_related = $cartProduct['isRelatedProduct'];
                $product->qty = $cartProduct['productQty'];
                $products[$index] = $product;
                $productsIds[] = $products[$index]->id;
                $index++;
            }
        }

        $relatedProduct = $this->relatedProduct->getRelatedProduct($productsIds);

        return Inertia::render(
            'Cart',
            [
                'products' => $products,
                'relatedProduct' => $relatedProduct,
                'shippingMethods' => $shippingMethods,
                'payments' => $paymentMethods,
            ],
        );
    }
}
