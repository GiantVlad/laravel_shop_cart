<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderActionRequest;
use App\Http\Resources\OrderCollection;
use App\Library\Services\IpspPaymentService;
use App\Library\Services\PaymentServiceInterface;
use App\Services\Order\OrderActions;
use App\Services\Order\OrderStatuses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Order;
use App\Services\Cart\CartService;
use Illuminate\View\View;

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
     * @param IpspPaymentService $paymentService
     * @return JsonResponse
     * @throws \Exception
     */
    public function doAction(OrderActionRequest $request, PaymentServiceInterface $paymentService): JsonResponse
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
            $order = $this->order->where(['id' => $orderId, 'user_id' => $user->id])->first();
            $requestData = [
                'order_id'    => $order->order_label,
                'order_desc'  => 'order #'. $order->order_label . '. Test Cart Number: 4444555511116666',
                'currency'    => 'USD',
                'amount'      => $order->total * 100,
                'response_url'=> url('checkout/success').'?_token='.csrf_token()
            ];
            
            //ToDo check if the order already exists in the payment system
            $responseData = $paymentService->pay($requestData)->getData();
            
            if (isset($responseData['checkout_url'])) {
                return response()->json(['redirect_to' => $responseData['checkout_url']]);
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
        $selectedOrder = $this->order->where(['id' => $id, 'user_id' => $userId])->with('orderData.product')->first();
        if (!$selectedOrder instanceof Order) {
            return  back()->with('error', 'Order not found');
        }

        return view('shop.order', ['order' => $selectedOrder]);
    }
}
