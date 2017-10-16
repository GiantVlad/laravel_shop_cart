<?php

namespace App\Http\Controllers;

use App\Product;
use App\RelatedProduct;
use App\Order;
use App\OrderData;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CartController extends Controller
{
    public function post (Request $request)
    {
        //All products has been removed (ajax)
        if (isset($request->input) && ($request->input == 'emptyCart')) {
            return view('empty-cart');
        }
        //Related product has removed (ajax)
        if (isset($request->input) && ($request->input == 'removeRelated')) {
            RelatedProduct::where('related_product_id', $request->id)->increment('points', -3);
            return 'success';
        }

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
                $price = Product::find( $result['productId'][$i] )->price;
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

    public function index ()
    {
        $product = Product::where('name', 'General product')->first();
        $product['is_related'] = 0;
        $product['qty'] = 1;
        $relatedProduct = RelatedProduct::leftJoin('products', 'products.id', '=', 'related_product_id')
            ->where('product_id', '=', $product['id'])->orderBy('points', 'desc')->first();

        return view('cart', ['products' => array($product), 'relatedProduct' => $relatedProduct]);
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

        $relatedProduct = RelatedProduct::leftJoin('products', 'products.id', '=', 'related_product_id')
            ->whereNotIn('products.id', $result['productId'])
            ->whereIn('product_id', $result['productId'])
            ->orderBy('points', 'desc')
            ->first();

        return ['products' => $products, 'relatedProduct' => $relatedProduct];
    }
}
