<?php

namespace App\Http\Controllers;

use App\ShippingMethod;
use Illuminate\Http\Request;


class AdminShippingMethodsController extends Controller
{
    private $shippingMethod;

    public function __construct (ShippingMethod $shippingMethod)
    {
        $this->middleware('auth:admin');
        $this->shippingMethod = $shippingMethod;
    }

    public function list ()
    {
        $shippingMethods = $this->shippingMethod->list();

        return view('admin.shipping-methods', ['shippingMethods' => $shippingMethods]);
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

    public function changeStatus(Request $request)
    {
        $this->validate($request, [
            'status' => 'required',
            'method_id' => 'required'
        ]);
        //$status = $request->status ? 1 : 0;
        $this->shippingMethod->where('id', $request->method_id)->update(['enable' => $request->status]);
        $shippingMethods = $this->shippingMethod->list();
        if ($request->ajax()) {
            return view('admin.shipping-methods-load', compact('shippingMethods'))->render();
        }
    }

    public function update (Request $request)
    {
        $this->validate($request, [

        ]);


    }

}
