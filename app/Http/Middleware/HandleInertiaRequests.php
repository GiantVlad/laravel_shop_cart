<?php

namespace App\Http\Middleware;

use App\Http\Resources\CartPostResource;
use App\Services\Cart\CartPostDTO;
use App\Services\Cart\CartService;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    public function __construct(readonly private CartService $cartService)
    {}
    
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $service = $this->cartService;
        return array_merge(parent::share($request), [
            'appName' => config('app.name'),
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'auth' => [
                'admin' => fn () => Auth::guard('admin')->user()
                    ? ['name' => Auth::guard('admin')->user()->name]
                    : null,
            ],
            
            'userName' => fn () => $request->user()
                ? $request->user()->name
                : false,
            
            'cart' => static function () use ($request, $service) {
                $user = $request->user();
                if ($user instanceof User) {
                    $cart = $service->getCart($user->id);
                    
                    return $cart
                        ? ['count' => max((count($cart) - 3), 0), 'total' => ($cart['total'] ?? 0)]
                        : null;
                }
                
                return null;
            }
        ]);
    }
}
