<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderActionRequest;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderDetailResource;
use App\Services\Order\OrderActions;
use App\Services\Order\OrderStatuses;
use App\Services\Payment\PaymentMethodManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Order;
use App\Services\Cart\CartService;
use Illuminate\View\View;
use Psr\SimpleCache\InvalidArgumentException;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     */
    private Order $order;
    private CartService $cartService;

    public function __construct(Order $order, CartService $cartService)
    {
        $this->order = $order;
        $this->cartService = $cartService;
        $this->middleware('auth')->except('logout');
    }
    
    /**
     * The list of orders.
     *
     * @param Request $request
     * @return View
     */
    public function list(Request $request): View
    {
        $userId = $request->user()->id;
        $orders = new OrderCollection(
            $this->order->getOrdersByUserId($userId)
                ->orderBy('created_at', 'desc')
                ->paginate(15)
        );
        
        return view('shop.orders', ['orders' => $orders]);
    }
    
    /**
     * @param OrderActionRequest $request
     * @param PaymentMethodManager $paymentManager
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    public function doAction(OrderActionRequest $request, PaymentMethodManager $paymentManager): JsonResponse
    {
        $validatedData = $request->validated();
        $orderAction = $validatedData['action'];
        $orderId = (int)$validatedData['id'];
        $user = $request->user();

        //For repeat order add products to cart and user will be redirected to the cart
        if ($orderAction === OrderActions::REPEAT) {
            $this->cartService->makeCartByOrderId($request->user()->id, $orderId);
            
            return response()->json(['redirect_to' => route('get.cart')]);
        }
        
        if ($orderAction === OrderActions::UNDO) {
            $order = $this->order->where(['id' => $orderId, 'user_id' => $user->id])->first();
            $order->status = OrderStatuses::DELETED;
            $order->save();
            
            return response()->json(['status' => $order->status]);
        }
    
        if ($orderAction === OrderActions::RE_PAYMENT) {
            /** @var Order $order */
            $order = $this->order->where(['id' => $orderId, 'user_id' => $user->id])->first();
            $requestData = [
                'order_id'    => $order->order_label,
                'paymentId' => $order->payments()->first()?->id,
                'order_desc'  => 'order #'. $order->order_label . '. Test Cart Number: 4444555511116666',
                'currency'    => 'USD',
                'amount'      => $order->total,
                'response_url'=> url('checkout/success').'?_token='.csrf_token()
            ];
            
            // ToDo check if the payment already exists in the payment system or create a new payment in our DB
            $paymentResponse = $paymentManager->pay($order->dispatches()->first()?->id, $requestData);
    
            if ($paymentResponse->getCheckoutUrl()) {
                return response()->json(['redirect_to' => $paymentResponse->getCheckoutUrl()]);
            }
        }
        
        return response()->json();
    }
    
    /**
     * @param int $id
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function getOrder(int $id, Request $request): View|RedirectResponse
    {
        $userId = $request->user()->id;
        $selectedOrder = $this->order
            ->where(['id' => $id, 'user_id' => $userId])
            ->first();
        
        if (!$selectedOrder instanceof Order) {
            return  back()->with('error', 'Order not found');
        }
        
        return view('shop.order', ['orderId' => $id]);
    }
    
    /**
     * @param int $id
     * @param Request $request
     * @return OrderDetailResource
     */
    public function getOrderData(int $id, Request $request): OrderDetailResource|RedirectResponse
    {
        $userId = $request->user()->id;
        $selectedOrder = $this->order->where(['id' => $id, 'user_id' => $userId])
            ->with('orderData.product')
            ->with('dispatches.method')
            ->with('payments.method')
            ->first();
        if (!$selectedOrder instanceof Order) {
            return  back()->with('error', 'Order not found');
        }

//        foreach ($selectedOrder->dispatches as &$dispatch) {
//            $dispatch = ShippingMethod::getLabel($dispatch->method->class_name);
//        }
        
        return new OrderDetailResource($selectedOrder);
    }
}
