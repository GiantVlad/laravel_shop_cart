<?php

namespace App\Http\Controllers;

use App\Order;
use App\ShippingMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AdminShippingMethodsController extends Controller
{
    private ShippingMethod $shippingMethod;
    private Order $order;

    public function __construct(ShippingMethod $shippingMethod, Order $order)
    {
        $this->shippingMethod = $shippingMethod;
        $this->order = $order;
    }
    
    /**
     * @return View
     */
    public function list(): Response
    {
        $shippingMethods = $this->shippingMethod->list()->map(static function (ShippingMethod $method): array {
            return [
                'id' => $method->id,
                'label' => $method->label,
                'priority' => $method->priority,
                'enabled' => (bool)$method->enable,
            ];
        });

        return Inertia::render('Admin/ShippingMethods/Index', ['shippingMethods' => $shippingMethods]);
    }
    
    /**
     * @param Request $request
     */
    public function delete(Request $request): void
    {
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
    public function changeStatus(Request $request): \Illuminate\Http\JsonResponse|string
    {
        $this->validate($request, [
            'status' => 'required',
            'method_id' => 'required'
        ]);
        //$status = $request->status ? 1 : 0;
        $this->shippingMethod->where('id', $request->method_id)->update(['enable' => $request->get('status')]);
        $shippingMethods = $this->shippingMethod->list()->map(static function (ShippingMethod $method): array {
            return [
                'id' => $method->id,
                'label' => $method->label,
                'priority' => $method->priority,
                'enabled' => (bool)$method->enable,
            ];
        });
        if ($request->expectsJson()) {
            return response()->json(['shippingMethods' => $shippingMethods]);
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
