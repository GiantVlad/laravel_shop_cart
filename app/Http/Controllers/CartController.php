<?php

namespace App\Http\Controllers;

use App\Order;
use App\OrderData;
use App\Product;
use App\RelatedProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct ()
    {
        $this->middleware('auth');
    }

    public function post (Request $request)
    {
        if (isset($request->input)) {
            $request->validate([
                'input' => 'string|min:5|max:30'
            ]);
            if ($request->input == 'emptyCart') { //All products has been removed (ajax)
                return view('empty-cart');
            } elseif ($request->input == 'removeRow') { //Product has been removed (ajax)
                if ($request->isRelated > 0) {
                    RelatedProduct::where('related_product_id', $request->id)->increment('points', -3);
                }

                if ($request->session()->has('cartProducts')) {
                    $cartProducts = $request->session()->get('cartProducts');
                    foreach ($cartProducts as $key => $cartProduct) {
                        if ($key == 'total') continue;

                        if ($cartProduct['productId'] == $request->id) {
                            $cartProducts = array_splice($cartProducts, (1 + $key), 1);
                            $cartProducts['total'] = $request->subtotal;
                            $request->session()->forget('cartProducts');
                            $request->session()->put('cartProducts', $cartProducts);

                            dd($request->session()->get('cartProducts'));

                            break;
                        }
                    }
                }
                return 'success';
            }
        }


        $request->validate(
            [
                'productId.*' => 'required|integer|min:1|max:99999',
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

        if ($request->has('addRelated')) {  // submit add Related Product
            return view('cart', $this->addRelatedProduct($request));
        } else {                                // submit pay
            $length = count($result['productId']);
            if ($result['related_product_id'] > 0) {
                RelatedProduct::where('related_product_id', $result['related_product_id'])->increment('points', -1);
            }
            $orderId = Order::insertGetId(
                [
                    'commentary' => '',
                    'total' => 0,
                    'status' => 'process',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ]
            );
            $subtotal = 0;
            for ($i = 0; $i < $length; $i++) {
                $price = Product::find($result['productId'][$i])->price;
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
                $subtotal += $price * $result['productQty'][$i];
                if ($result['isRelatedProduct'][$i] == 1) {
                    RelatedProduct::where('related_product_id', $result['productId'][$i])
                        ->increment('points', 5);
                }
            }
            Order::where('id', $orderId)->update(['total' => $subtotal]);

            if (session()->has('cartProducts')) session()->forget('cartProducts');

            return view('success');
        }
    }

    public function index ()
    {
        if (!session()->has('cartProducts')) return view('empty-cart');

        $cartProducts = session('cartProducts');

        $products = [];
        $index = 0;
        $productsIds = [];

        foreach ($cartProducts as $key => $cartProduct) {

            if ($key === 'total') continue;

            $products[$index] = Product::find($cartProduct['productId']);
            //$products[$index]->is_related = $cartProduct['isRelatedProduct'];
            $products[$index]->qty = $cartProduct['productQty'];
            $productsIds[] = $products[$index]->id;
            $index++;
        }
        $relatedProduct = RelatedProduct::leftJoin('products', 'products.id', '=', 'related_product_id')
            ->whereIn('product_id', $productsIds)->orderBy('points', 'desc')->first();
        return view('cart', ['products' => $products, 'relatedProduct' => $relatedProduct]);
    }

    public function addToCart (Request $request)
    {
        $request->validate(
            [
                'productId' => 'required|integer|min:1|max:99999',
                'productQty' => 'required|integer|min:1|max:99'
            ],
            [
                'productQty.required' => 'The Quantity field can not be blank.',
                'productQty.integer' => 'Quantity must be integer.',
                'productQty.min' => 'Minimum of Quantity is 1 psc.',
                'productQty.max' => 'Maximum of Quantity is 99 psc.'
            ]);

        $cartProducts = $request->session()->get('cartProducts');
        $qty = $request->get('productQty');

        //If product exist in the cart
        if (!empty($cartProducts)) {
            foreach ($cartProducts as $key => $cartProduct) {
                if ($key == 'total') continue;
                if (!empty(
                    array_intersect_assoc(['productId' => $request->get('productId')], $cartProduct)
                )) {
                    $qty += $cartProduct['productQty'];
                    $cartProducts[$key] = ['productId' => $request->get('productId'), 'productQty' => $qty];
                    $request->session()->forget('cartProducts');
                    $request->session()->put('cartProducts', $cartProducts);
                    auth()->user()->cart = serialize(session()->get('cartProducts'));
                    auth()->user()->save;
                    return redirect('cart');
                }
            }
        }

        $total = Product::find($request->get('productId'))->price * $qty;
        $request->session()->put('cartProducts.total', $total);
        $request->session()->push('cartProducts',
            ['productId' => $request->get('productId'), 'productQty' => $qty, 'isRelatedProduct' => 0]
        );
        return redirect('cart');
    }

    private function addRelatedProduct (Request $request)
    {
        $result = $request->all();
        array_push($result['productId'], $result['related_product_id']);
        array_push($result['productQty'], 1);
        array_push($result['isRelatedProduct'], 1);

        $products = Product::whereIn('id', $result['productId'])->get();
        foreach ($products as $key => $product) {
            $product['is_related'] = $result['isRelatedProduct'][$key];
            $product['qty'] = $result['productQty'][$key];
        }

        //add to session
        $request->session()->push('cartProducts',
            ['productId' => $result['productId'], 'productQty' => $result['productQty'], 'isRelatedProduct' => $result['isRelatedProduct']]
        );

        $relatedProduct = RelatedProduct::leftJoin('products', 'products.id', '=', 'related_product_id')
            ->whereNotIn('products.id', $result['productId'])
            ->whereIn('product_id', $result['productId'])
            ->orderBy('points', 'desc')
            ->first();

        return ['products' => $products, 'relatedProduct' => $relatedProduct];
    }
}
