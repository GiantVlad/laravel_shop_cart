<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$email = 'qa.order@example.test';
$user = App\User::firstOrCreate(['email' => $email], ['name' => 'QA Order', 'password' => Illuminate\Support\Facades\Hash::make('qa-order-pass')]);
App\Order::where('user_id', $user->id)->delete();

$ship = App\ShippingMethod::first()->id;
$pay  = App\PaymentMethod::first()->id;

$repo = app(App\Repositories\OrderRepository::class);
$products = App\Product::take(3)->get()->pluck('id')->all();

$o1 = $repo->createOrder($user, [
  'subtotal' => 123.45, 'product_ids' => $products,
  'isRelatedProduct' => [0,0,1], 'productQty' => [2,1,3],
  'shippingMethodId' => $ship, 'paymentMethodId' => $pay,
]);
$p = $o1->payments()->first();
$p->external_id = 'PAY-8842-1177';
$p->status = App\Payment::STATUS_PAID;
$p->save();

$o2 = $repo->createOrder($user, [
  'subtotal' => 49.99, 'product_ids' => [$products[0]],
  'isRelatedProduct' => [0], 'productQty' => [1],
  'shippingMethodId' => $ship, 'paymentMethodId' => $pay,
]);
$o2->status = 'completed'; $o2->save();

$o3 = $repo->createOrder($user, [
  'subtotal' => 0, 'product_ids' => [], 'isRelatedProduct' => [], 'productQty' => [],
  'shippingMethodId' => $ship, 'paymentMethodId' => $pay,
]);
$o3->dispatches()->delete(); $o3->payments()->delete();

echo "user={$user->id} full={$o1->id} completed={$o2->id} empty={$o3->id}\n";
