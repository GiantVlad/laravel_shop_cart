<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Events\PaymentCreated;
use App\Http\Requests\CheckoutRequest;
use App\Repositories\OrderRepository;
use App\Services\Cart\CartService;
use App\Services\Payment\PaymentMethodManager;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Order;
use Illuminate\Routing\Redirector;
use App\RelatedProduct;

class CheckoutController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        private readonly RelatedProduct $relatedProduct,
        private readonly CartService $cartService,
        private readonly Dispatcher $eventDispatcher,
    ) {
        $this->middleware('auth')->except('logout');
    }
    
    /**
     * @param CheckoutRequest $request
     * @param OrderRepository $orderRepository
     * @param PaymentMethodManager $paymentManager
     * @return JsonResponse
     * @throws \Throwable
     */
    public function sendPayment(
        CheckoutRequest      $request,
        OrderRepository      $orderRepository,
        PaymentMethodManager $paymentManager,
    ): JsonResponse {
        $requestData = $request->validated();
        
        if (isset($requestData['related_product_id']) && (int)$requestData['related_product_id'] > 0) {
            /** @var RelatedProduct $relatedProduct */
            $relatedProduct = $this->relatedProduct->find((int)$requestData['related_product_id']);
            $relatedProduct->increment('points', -1);
        }

        $subtotal = $requestData['subtotal'];
        $user = $request->user();
        
        $order = $orderRepository->createOrder(
            $user,
            $requestData
        );
    
        $this->eventDispatcher->dispatch(new OrderCreated($order));
    
        $this->cartService->forget($user->id);
    
        $paymentRequestData = [
            'order_id' => $order->order_label,
            'paymentId' => $order->payments()->first()?->id,
            'order_desc' => 'order #' . $order->order_label . '. Test Cart Number: 4444555511116666',
            'currency' => 'USD',
            'amount' => $subtotal,
            'response_url' => url('checkout/success') . '?_token=' . csrf_token(),
        ];
    
        $paymentResponse = $paymentManager->pay($requestData['paymentMethodId'], $paymentRequestData);
        
        if ($paymentResponse->getCheckoutUrl()) {
            return response()->json(['redirect_to' => $paymentResponse->getCheckoutUrl()]);
        }
        
        return response()->json(['redirect_to' => route('orders')]);
    }
    
    /**
     * @param Request $request
     * @return RedirectResponse|Redirector
     */
    public function success(Request $request): RedirectResponse|Redirector
    {
        if (!empty($request->get('response_status')) && ($request->get('response_status') === 'success')) {
            Order::where('order_label', $request->get('order_id'))->update(['status' => 'process']);
            
            return redirect('checkout/success')->with(
                    'message',
                    "Thanks. Your payment has been successfully completed! The Order #" .
                    $request->get('order_id') . " has been created."
            );
        }
        
        return redirect('checkout/success')->with('error', 'Payment is NOT successful. PLS, try again');
    }
}
