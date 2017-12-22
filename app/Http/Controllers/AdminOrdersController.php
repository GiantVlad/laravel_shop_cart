<?php

namespace App\Http\Controllers;

use App\User;
use App\Order;
use App\OrderData;
use Illuminate\Http\Request;


class AdminOrdersController extends Controller
{
    private $user;
    private $order;
    private $orderData;

    public function __construct (User $user, Order $order, OrderData $orderData)
    {
        $this->middleware('auth:admin');
        $this->user = $user;
        $this->order = $order;
        $this->orderData = $orderData;
    }

    public function list ()
    {
        $orders = $this->order->paginate(15);
        return view('admin.orders', ['orders' => $orders]);
    }

    public function delete (Request $request)
    {

    }

    public function search(Request $request)
    {
        $keyword = $request->keyword;

        if ($keyword!='') {
            $selectedOrder = $this->order->getOrderByID($keyword);

            return view('admin.orders', ['orders' => $selectedOrder]);
        }

        return back();
    }


    public function showEditForm (int $id)
    {


    }

    public function update (Request $request)
    {
        $this->validate($request, [

        ]);


    }

}
