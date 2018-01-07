<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Order;
use App\OrderData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Product;
use App\RelatedProduct;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $order;


    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->middleware('auth')->except('logout');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $userId = Auth::user()->id;
        $orders = $this->order->getOrdersByUserId($userId)->orderBy('created_at', 'desc')->paginate(15);

        return view('shop.orders',['orders' => $orders]);
    }

    public function getOrder(int $id)
    {
        $userId = Auth::user()->id;
        $selectedOrder = $this->order->where([['id', '=', $id],['user_id', '=', $userId]])->first();
        if (empty($selectedOrder)) return  back()->with('error', 'Order not found');

        return view('shop.order',['order' => $selectedOrder]);
    }
}
