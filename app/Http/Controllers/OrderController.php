<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Order;
use App\OrderData;
use Carbon\Carbon;
use App\Library\Services\CartService;
use Illuminate\Support\Facades\Auth;
use App\Product;
use App\RelatedProduct;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $order;
    private $cartService;
    private $orderData;

    public function __construct(Order $order, CartService $cartService, OrderData $orderData)
    {
        $this->order = $order;
        $this->orderData = $orderData;
        $this->cartService = $cartService;
        $this->middleware('auth')->except('logout');
    }

    /**
     * Show the application dashboard.
     *
     * @return View
     */
    public function list()
    {
        $userId = Auth::user()->id;
        $orders = $this->order->getOrdersByUserId($userId)->orderBy('created_at', 'desc')->paginate(15);
        return view('shop.orders', ['orders' => $orders]);
    }

    public function changeOrderStatus(Request $request)
    {
        $orderStatus = $request->get('status');
        $orderId = $request->get('id');

        //For repeat order add products to cart and user will be redirected to cart
        if ($orderStatus === 'repeat') {
            $this->makeCartByOrderId($orderId);
            return 'redirect_to_cart';
        }

        $userId = Auth::user()->id;
        $order = $this->order->where([['id', '=', $orderId],['user_id', '=', $userId]])->first();
        $order->status = $orderStatus;
        $order->save();

        $html = view('shop.order-status-options', compact('order'))->render();

        return response()->json(compact('html'));
    }

    public function getOrder(int $id)
    {
        $userId = Auth::user()->id;
        $selectedOrder = $this->order->where([['id', '=', $id],['user_id', '=', $userId]])->with('orderData.product')->first();
        if (empty($selectedOrder)) return  back()->with('error', 'Order not found');

        return view('shop.order',['order' => $selectedOrder]);
    }

    private function makeCartByOrderId(int $orderId)
    {
        $orderDetails = $this->orderData->where('order_id', $orderId)->get();
        if (empty($orderDetails)) {
            //Todo return error
            return 'error';
        }
        foreach ($orderDetails as $orderRow) {
            $this->cartService->addToCart($orderRow->product_id, $orderRow->qty);
        }
        return;
    }
}
