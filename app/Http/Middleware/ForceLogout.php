<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class ForceLogout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        // You might want to create a method on your model to
        // prevent direct access to the `logout` property. Something
        // like `markedForLogout()` maybe.
        if (! empty($user->force_logout)) {
            // Not for the next time!
            // Maybe a `unmarkForLogout()` method is appropriate here.
            $user->force_logout = false;
            $user->save();

            // Log her out
            Auth::guard('web')->logout();

            return redirect()->route('login');
        }

        return $next($request);
    }
}
