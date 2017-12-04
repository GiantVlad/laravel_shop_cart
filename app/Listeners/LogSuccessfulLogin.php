<?php

namespace App\Listeners;

use App\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = auth()->user();
        if (!$user) return;
        $cart = unserialize($user->cart);
        if ($cart) session()->put('cartProducts', $cart);

    }
}
