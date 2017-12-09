<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Catalog;

class AdminProductsController extends Controller
{
    public function __construct ()
    {
        $this->middleware('auth:admin');
    }

    public function list ()
    {
        //todo app settings
        $products = Product::paginate(15);
        $categories = Catalog::all('id', 'name');
        return view('admin.products', ['products' => $products, 'categories' => $categories]);
    }

    public function categoryFilter (Request $request, $category_id)
    {
        $products = $category_id ?
            Product::where('catalog_id', $category_id)->paginate(10) :
            Product::paginate(10);

        if ($request->ajax()) {
            return view('admin.products-load', compact('products'))->render();
        }

        $categories = Catalog::all('id', 'name');
        return view('admin.products', ['products' => $products, 'categories' => $categories]);
    }

    public function delete (Request $request)
    {

        if (!$request->id) return back()->withErrors('Server Error... Please try again.');

        $product = Product::find($request->id);

        if (!$product) return back()->withErrors('Server Error... Product not found');

        $product->delete();
        return redirect()->back()->with('message', 'Product ' . $product->name . ' was deleted!');
    }

    public function showEditForm ($id = null)
    {
        $categories = Catalog::all('id', 'name');
        $product = null;
        if ($id) $product = Product::find($id);
        if ($id && !$product) return back()->withErrors('Server Error... Product not found');

        return view('admin.edit-product', ['product' => $product, 'categories' => $categories]);
    }

    public function update (Request $request)
    {
        $this->validate($request, [
            'name' => 'required | min:3 | max:150',
            'price' => 'required | min:1 | max:10',
            'image' => 'image | mimes:jpeg,png,jpg,gif,svg | max:2048',
            'category' => 'required'
        ]);
        if ($request->id) {
            $product = Product::find($request->id);
            $message = 'Product ' . $request->name . ' was changed!';
        } else {
            $product = new Product;
            $message = 'Product ' . $request->name . ' was added!';
        }

        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->catalog_id = $request->category;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'prod_' . time() . '.' . $image->getClientOriginalExtension();
            $imageDestinationPath = public_path('images/products/');
            $image->move($imageDestinationPath, $imageName);
            $product->image = 'products/' . $imageName;
        }
        //todo image resize

        $product->save();

        return redirect(url('admin/products'))->with('message', $message);
    }
}
