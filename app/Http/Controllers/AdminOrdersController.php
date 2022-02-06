<?php

namespace App\Http\Controllers;

use App\User;
use App\Order;
use App\OrderData;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminOrdersController extends Controller
{
    public function __construct(private Order $order)
    {
        $this->middleware('auth:admin');
    }
    
    /**
     * @return View
     */
    public function list(): View
    {
        $orders = $this->order->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.orders', ['orders' => $orders]);
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
        $keyword = $request->get('keyword');

        if (empty($keyword)) {
            return back();
        }

        $selectedOrder = $this->order->getOrderByLabel($keyword)->paginate(15);
        
        return view('admin.orders', ['orders' => $selectedOrder]);
    }
    
    /**
     * @param int $id
     */
    public function showEditForm(int $id): void
    {
    }
    
    /**
     * @param Request $request
     */
    public function update(Request $request): void
    {
    }
}
