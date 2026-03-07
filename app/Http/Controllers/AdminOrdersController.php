<?php

namespace App\Http\Controllers;

use App\User;
use App\Order;
use App\OrderData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminOrdersController extends Controller
{
    public function __construct(private Order $order)
    {
        $this->middleware('auth:admin');
    }
    
    /**
     * @return View
     */
    public function list(): Response
    {
        $orders = $this->order
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->through(static function (Order $order): array {
                return [
                    'id' => $order->id,
                    'label' => $order->order_label,
                    'createdAt' => $order->created_at?->format('Y-M-d H:i'),
                    'userName' => $order->user?->name,
                    'userEmail' => $order->user?->email,
                    'total' => $order->total,
                    'status' => $order->status,
                ];
            });

        return Inertia::render('Admin/Orders/Index', ['orders' => $orders, 'keyword' => null]);
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
    public function search(Request $request): Response|RedirectResponse
    {
        $keyword = $request->get('keyword');

        if (empty($keyword)) {
            return back();
        }

        $selectedOrder = $this->order
            ->getOrderByLabel($keyword)
            ->with('user')
            ->paginate(15)
            ->through(static function (Order $order): array {
                return [
                    'id' => $order->id,
                    'label' => $order->order_label,
                    'createdAt' => $order->created_at?->format('Y-M-d H:i'),
                    'userName' => $order->user?->name,
                    'userEmail' => $order->user?->email,
                    'total' => $order->total,
                    'status' => $order->status,
                ];
            });
        
        return Inertia::render('Admin/Orders/Index', ['orders' => $selectedOrder, 'keyword' => $keyword]);
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
