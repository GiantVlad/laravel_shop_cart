<?php

namespace App\Http\Controllers;

use App\Order;
use App\OrderData;
use App\Product;
use App\RelatedProduct;
use App\ShippingMethod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $product;
    private $shippingMethod;
    private $relatedProduct;

    public function __construct (Product $product,
                                 ShippingMethod $shippingMethod,
                                 RelatedProduct $relatedProduct)
    {
        $this->product = $product;
        $this->shippingMethod = $shippingMethod;
        $this->relatedProduct = $relatedProduct;

        $this->middleware('auth')->except('logout');
    }

    public function post (Request $request)
    {
        if (isset($request->input)) {
            $request->validate([
                'input' => 'string|min:5|max:30'
            ]);
            if ($request->input == 'emptyCart') { //All products has been removed (ajax)
                return view('empty-cart');
            } elseif ($request->get('input') == 'removeRow') { //Product has been removed (ajax)

                $productId = $request->get('productId');

                if ($request->get('isRelated') > 0) {
                    $this->relatedProduct->where('id', $productId)->increment('points', -3);
                }

                if ($request->session()->has('cartProducts')) {

                    $cartProducts = $request->session()->get('cartProducts');

                    if (array_key_exists($productId, $cartProducts)) {

                        unset($cartProducts[$productId]);
                        if (count($cartProducts) <= 2) {
                            $request->session()->forget('cartProducts');
                            return redirect('cart');
                        }
                        $cartProducts['total'] = $request->get('subtotal');
                        $request->session()->forget('cartProducts');
                        $request->session()->put('cartProducts', $cartProducts);
                    }
                    return ['items' => (count($cartProducts) - 2), 'total' => $cartProducts['total']];
                }
                return 0;
            } elseif ($request->get('input') == 'changeShipping') {

                if ($request->session()->has('cartProducts')) {

                    $cartProducts = $request->session()->get('cartProducts');

                    if (array_key_exists('total', $cartProducts)) {
                        $cartProducts['total'] = $request->subtotal;
                        $cartProducts['shippingMethodId'] = $request->shippingMethodId;
                        $request->session()->forget('cartProducts');
                        $request->session()->put('cartProducts', $cartProducts);
                    }
                    return ['total' => $cartProducts['total']];
                }
            } elseif ($request->get('input') == 'addRelated') {
                $this->addRelatedProduct($request);
                return 'ok';
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

            // submit pay
            $length = count($result['productId']);
            if (!empty($result['related_product_id']) && $result['related_product_id'] > 0) {
                $this->relatedProduct->find($result['related_product_id'])->increment('points', -1);
            }
            $orderId = Order::insertGetId(
                [
                    'commentary' => '',
                    'total' => 0,
                    'status' => 'process',
                    'user_id' => Auth::user()->id,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ]
            );
            $subtotal = 0;
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
                $subtotal += $price * $result['productQty'][$i];
                if ($result['isRelatedProduct'][$i] == 1) {
                    $this->relatedProduct->find($result['productId'][$i])->increment('points', 5);
                }
            }
            Order::where('id', $orderId)->update(['total' => $subtotal]);

            if (session()->has('cartProducts')) session()->forget('cartProducts');

            return view('success');
    }

    public function index ()
    {
        $cartProducts = session('cartProducts');

        if (empty($cartProducts)) return view('empty-cart');

        $products = [];
        $index = 0;
        $productsIds = [];

        $shippingMethods = $this->shippingMethod->getAllEnabled();
        $shippingMethods->selected = '';
        foreach ($cartProducts as $key => $cartProduct) {
            if ($key === 'total') continue;
            if ($key === 'shippingMethodId') {
                $shippingMethods->selected = $cartProducts[$key];
                continue;
            }
            $products[$index] = $this->product->find($key);
            $products[$index]->is_related = $cartProduct['isRelatedProduct'];
            $products[$index]->qty = $cartProduct['productQty'];
            $productsIds[] = $products[$index]->id;
            $index++;
        }
        $relatedProduct = $this->relatedProduct->getRelatedProduct($productsIds);

        return view('cart', ['products' => $products, 'relatedProduct' => $relatedProduct, 'shippingMethods' => $shippingMethods]);
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
        $productId = $request->get('productId');
        $qty = $request->get('productQty');
        $total = 0;
        //If product exist in the cart
        if (!empty($cartProducts)) {
            $total = $cartProducts['total'];
            if (array_key_exists($productId, $cartProducts)) {
                $total += $this->product->getProductPriceById($productId) * $qty;
                $cartProducts['total'] = $total;
                $qty += $cartProducts[$productId]['productQty'];
                $cartProducts[$productId] = ['productQty' => $qty, 'isRelatedProduct' => $cartProducts[$productId]['isRelatedProduct']];
                $request->session()->forget('cartProducts');
                $request->session()->put('cartProducts', $cartProducts);
                if ($request->ajax()) return ['items' => (count($cartProducts) - 1), 'total' => $cartProducts['total']];
                return redirect('cart');
            }
        }

        $total += $this->product->getProductPriceById($request->get('productId')) * $qty;
        $request->session()->put('cartProducts.total', $total);
        $request->session()->put('cartProducts.shippingMethodId', '');
        $request->session()->put('cartProducts.' . $request->get('productId'),
            ['productQty' => $qty, 'isRelatedProduct' => 0]
        );
        if ($request->ajax()) return ['items' => (count($request->session()->get('cartProducts')) - 2), 'total' => $request->session()->get('cartProducts.total')];
        return redirect('cart');
    }

    private function addRelatedProduct (Request $request)
    {
        //add to session
        $request->session()->put('cartProducts.' . $request->related_product_id,
            ['productQty' => 1, 'isRelatedProduct' => 1]
        );
        $cartProducts = session('cartProducts');
        foreach ($cartProducts as $key => $cartProduct) {
            if ($key === 'total' || $key === 'shippingMethodId') continue;
            $productsIds[] = $key;
        }

        $relatedProduct = $this->relatedProduct->getRelatedProduct($productsIds);

        return $relatedProduct;
    }
}
