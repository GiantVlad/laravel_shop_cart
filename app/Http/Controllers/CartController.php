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
    public function post (Request $request)
    {
        if (isset($request->input)) {
            $request->validate([
                'input' => 'string|min:5|max:30'
            ]);
            if ($request->input == 'emptyCart') { //All products has been removed (ajax)
                return view('empty-cart');
            } elseif ($request->input == 'removeRelated'){ //Related product has removed (ajax)
                RelatedProduct::where('related_product_id', $request->id)->increment('points', -3);
                return 'success';
            }
        }

        $request->validate([
            'productId.*' => 'required|integer|min:1|max:99999',
            'related_product_id' => 'integer|max:99999',
            'isRelatedProduct.*' => 'required|boolean',
            'productQty.*' => 'required|integer|min:1|max:99'
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
            return view('success');
        }
    }

    public
    function index ()
    {
        $product = Product::where('name', 'General product')->first();
        $product['is_related'] = 0;
        $product['qty'] = 1;
        $relatedProduct = RelatedProduct::leftJoin('products', 'products.id', '=', 'related_product_id')
            ->where('product_id', '=', $product['id'])->orderBy('points', 'desc')->first();

        return view('cart', ['products' => array($product), 'relatedProduct' => $relatedProduct]);
    }

    private
    function addRelatedProduct (Request $request)
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

        $relatedProduct = RelatedProduct::leftJoin('products', 'products.id', '=', 'related_product_id')
            ->whereNotIn('products.id', $result['productId'])
            ->whereIn('product_id', $result['productId'])
            ->orderBy('points', 'desc')
            ->first();

        return ['products' => $products, 'relatedProduct' => $relatedProduct];
    }
}
