<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Library\Services\PaymentServiceInterface;
use App\Repositories\OrderRepository;
use App\Services\Cart\CartService;
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
        private RelatedProduct $relatedProduct,
        private CartService $cartService,
    ) {
        $this->middleware('auth')->except('logout');
    }
    
    /**
     * @param CheckoutRequest $request
     * @param PaymentServiceInterface $paymentService
     * @param OrderRepository $orderRepository
     * @return JsonResponse
     */
    public function sendPayment(
        CheckoutRequest $request,
        PaymentServiceInterface $paymentService,
        OrderRepository $orderRepository
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

        
        $this->cartService->forget($user->id);
        
        $paymentRequestData = [
            'order_id' => $order->order_label,
            'order_desc' => 'order #' . $order->order_label . '. Test Cart Number: 4444555511116666',
            'currency' => 'USD',
            'amount' => $subtotal * 100,
            'response_url' => url('checkout/success') . '?_token=' . csrf_token()
        ];

        $paymentResponse = $paymentService->pay($paymentRequestData);
        $paymentResponseData = $paymentResponse->getData();
        
        if (isset($paymentResponseData['checkout_url'])) {
            return response()->json(['redirect_to' => $paymentResponseData['checkout_url']]);
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
                    "Thank's. Your payment has been successfully completed! The Order #" .
                    $request->get('order_id') . " has been created."
            );
        }
        
        return redirect('checkout/success')->with('error', 'Payment is NOT successful. PLS, try again');
    }
}
