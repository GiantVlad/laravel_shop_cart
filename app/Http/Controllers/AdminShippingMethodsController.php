<?php

namespace App\Http\Controllers;

use App\Order;
use App\ShippingMethod;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminShippingMethodsController extends Controller
{
    private ShippingMethod $shippingMethod;
    private Order $order;

    public function __construct(ShippingMethod $shippingMethod, Order $order)
    {
        $this->middleware('auth:admin');
        $this->shippingMethod = $shippingMethod;
        $this->order = $order;
    }
    
    /**
     * @return View
     */
    public function list(): View
    {
        $shippingMethods = $this->shippingMethod->list();

        return view('admin.shipping-methods', ['shippingMethods' => $shippingMethods]);
    }
    
    /**
     * @param Request $request
     */
    public function delete(Request $request): void
    {
    }
    
    /**
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function search(Request $request): View|RedirectResponse
    {
        $keyword = $request->keyword;

        if ($keyword!='') {
            $selectedOrder = $this->order->getOrderById($keyword);

            return view('admin.orders', ['orders' => $selectedOrder]);
        }

        return back();
    }
    
    
    /**
     * @param int $id
     */
    public function showEditForm (int $id): void
    {
    }
    
    /**
     * @param Request $request
     * @return string
     * @throws ValidationException
     */
    public function changeStatus(Request $request): string
    {
        $this->validate($request, [
            'status' => 'required',
            'method_id' => 'required'
        ]);
        //$status = $request->status ? 1 : 0;
        $this->shippingMethod->where('id', $request->method_id)->update(['enable' => $request->get('status')]);
        $shippingMethods = $this->shippingMethod->list();
        if ($request->ajax()) {
            return view('admin.shipping-methods-load', compact('shippingMethods'))->render();
        }
        
        return '';
    }
    
    /**
     * @param Request $request
     */
    public function update(Request $request): void
    {
    }
}
