<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ipsp\Api;
use App\Order;
use App\OrderData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Product;
use App\RelatedProduct;

class CheckoutController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $ipsp;
    private $product;
    private $relatedProduct;


    public function __construct(Api $ipsp,
                                Product $product,
                                RelatedProduct $relatedProduct)
    {
        $this->product = $product;
        $this->relatedProduct = $relatedProduct;
        $this->ipsp = $ipsp;

        $this->middleware('auth')->except('logout');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendPayment(Request $request)
    {
        $request->validate(
            [
                'productId.*' => 'required|integer|min:1|max:99999',
                'subtotal' => 'required',
                'related_product_id' => 'integer|max:99999',
                'isRelatedProduct.*' => 'required|boolean',
                'productQty.*' => 'required|integer|min:1|max:99'
            ],
            [
                'productQty.*.required' => 'The Quantity field can not be blank.',
                'productQty.*.integer' => 'Quantity must be integer.',
                'productQty.*.min' => 'Minimum of Quantity is 1 psc.',
                'productQty.*.max' => 'Maximum of Quantity is 99 psc.'
            ]);

        $result = $request->all();

        // submit pay
        $length = count($result['productId']);
        if (!empty($result['related_product_id']) && $result['related_product_id'] > 0) {
            $this->relatedProduct->find($result['related_product_id'])->increment('points', -1);
        }
        $order_label = env('APP_ORDERS_PREFIX', 'ule').'_'.time();

        $subtotal = $request->get('subtotal');

        $orderId = Order::insertGetId(
            [
                'commentary' => '',
                'total' => $subtotal,
                'status' => 'pending payment',
                'order_label' => $order_label,
                'user_id' => Auth::user()->id,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]
        );

        for ($i = 0; $i < $length; $i++) {
            $price = $this->product->find($result['productId'][$i])->price;
            OrderData::insert(
                [
                    'order_id' => $orderId,
                    'product_id' => $result['productId'][$i],
                    'is_related_product' => $result['isRelatedProduct'][$i],
                    'price' => $price,
                    'qty' => $result['productQty'][$i],
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ]
            );

            if ($result['isRelatedProduct'][$i] == 1) {
                $this->relatedProduct->find($result['productId'][$i])->increment('points', 5);
            }
        }

        if (session()->has('cartProducts')) session()->forget('cartProducts');

        $data = $this->ipsp->call('checkout',array(
            'order_id'    => $order_label,
            'order_desc'  => 'order # '.$order_label,
            'currency'    => $this->ipsp::USD,
            'amount'      => $subtotal*100,
            'response_url'=> url('checkout/success').'?_token='.csrf_token()
        ))->getResponse();
        // redirect to checkoutpage
        //ToDo check if $data->checkout_url exist
        return redirect($data->checkout_url);

        //return view('order-not-success');
    }

    public function success (Request $request)
    {
        if (!empty($request->response_status) && ($request->response_status === 'success')) {
            Order::where('order_label', $request->order_id)->update(['status' => 'process']);
            return view('order-success', ['orderLabel' =>$request->order_id]);
        }
        return view('order-not-success');
    }
}
