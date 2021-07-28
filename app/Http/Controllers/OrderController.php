<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderActionRequest;
use App\Http\Resources\OrderCollection;
use App\Services\Order\OrderActions;
use App\Services\Order\OrderStatuses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Order;
use App\OrderData;
use App\Library\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     */
    private Order $order;
    private CartService $cartService;
    private OrderData $orderData;

    public function __construct(Order $order, CartService $cartService, OrderData $orderData)
    {
        $this->order = $order;
        $this->orderData = $orderData;
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
     * @return JsonResponse
     * @throws \Exception
     */
    public function doAction(OrderActionRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $orderAction = $validatedData['action'];
        $orderId = (int)$validatedData['id'];
        $user = $request->user();

        //For repeat order add products to cart and user will be redirected to the cart
        if ($orderAction === OrderActions::REPEAT) {
            $this->makeCartByOrderId($orderId);
            
            return response()->json('redirect_to_cart');
        }
        
        if ($orderAction === OrderActions::UNDO) {
            $order = $this->order->where([['id', '=', $orderId], ['user_id', '=', $user->id]])->first();
            $order->status = OrderStatuses::DELETED;
            $order->save();
            
            return response()->json(['status' => $order->status]);
        }
        
        // ToDo implement all actions
        return response()->json('redirect_to_cart');
//        $userId = Auth::user()->id;

//
//        $html = \View::make('shop.order-status-options', compact('order'))->render();
//
//        return response()->json(compact('html'));
    }

    public function getOrder(int $id)
    {
        $userId = Auth::user()->id;
        $selectedOrder = $this->order->where([['id', '=', $id],['user_id', '=', $userId]])->with('orderData.product')->first();
        if (empty($selectedOrder)) return  back()->with('error', 'Order not found');

        return view('shop.order',['order' => $selectedOrder]);
    }
    
    /**
     * @param int $orderId
     * @throws \Exception
     */
    private function makeCartByOrderId(int $orderId): void
    {
        $orderDetails = $this->orderData->where(['order_id' => $orderId])->get();
        if ($orderDetails->isEmpty()) {
            //Todo make useful Exception
            throw new \Exception();
        }
        foreach ($orderDetails as $orderRow) {
            $this->cartService->addToCart($orderRow->product_id, $orderRow->qty);
        }
    }
}
