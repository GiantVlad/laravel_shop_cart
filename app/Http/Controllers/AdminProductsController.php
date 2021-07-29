<?php

namespace App\Http\Controllers;

use App\Property;
use App\PropertyValue;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Product;
use App\Catalog;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidationException;

class AdminProductsController extends Controller
{
    public function __construct ()
    {
        $this->middleware('auth:admin');
    }

    public function list(): View
    {
        //todo app settings
        $products = Product::paginate(15);
        $categories = Catalog::all('id', 'name');
        
        return view('admin.products', ['products' => $products, 'categories' => $categories]);
    }
    
    /**
     * @param Request $request
     * @param int|null $category_id
     * @return View|string
     */
    public function categoryFilter(Request $request, ?int $category_id): View|string
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
    
    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        if (!$request->get('id')) {
            return back()->withErrors('Server Error... Please try again.');
        }
        $id = (int)$request->get('id');
        
        $product = Product::findOrFail($id);

        if (!$product instanceof Product) {
            return back()->withErrors('Server Error... Product not found');
        }

        $product->delete();
        
        return redirect()->back()->with('message', 'Product ' . $product->name . ' was deleted!');
    }

    /**
     * Remove property from product
     * @param Request $request
     * @param int $product_id
     * @return int|RedirectResponse
     */
    public function deleteProperty(Request $request, int $product_id): int|RedirectResponse
    {
        if (!$request->get('value_id')) {
            return back()->withErrors('Server Error... Please try again.');
        }
        $valueId = (int)$request->get('value_id');
        $product = Product::findOrFail($product_id);

        if (!$product instanceof Product) {
            return back()->withErrors('Server Error... Product not found');
        }

        $product->properties()->detach([$valueId]);

        return $valueId;
    }
    
    /**
     * @param int|null $id
     * @return View|RedirectResponse
     */
    public function showEditForm(int $id = null): View|RedirectResponse
    {
        $categories = Catalog::all('id', 'name');
        $product = null;
        if ($id) {
            $product = Product::with('properties')->findOrFail($id);
            if (!$product instanceof Product) {
                return back()->withErrors('Server Error... Product not found');
            }
        }
        
        foreach ($product->properties as $property) {
            if ($property->properties->type === 'selector') {
                $property->properties->selectProperties =
                    PropertyValue::where('property_id', $property->properties->id)->pluck('value', 'id');
            }
        }
        
        return view('admin.edit-product', ['product' => $product, 'categories' => $categories]);
    }
    
    /**
     * @param Request $request
     * @return RedirectResponse|Redirector
     * @throws ValidationException
     */
    public function update(Request $request): RedirectResponse|Redirector
    {
        $this->validate($request, [
            'name' => 'required | min:3 | max:150',
            'price' => 'required | min:1 | max:10',
            'image' => 'image | mimes:jpeg,png,jpg,gif,svg | max:2048',
            'category' => 'required'
        ]);
        $id = (int)$request->get('id');
        if ($id) {
            $product = Product::findOrFail($id);
            $message = 'Product ' . $request->name . ' was changed!';
        } else {
            $product = new Product;
            $message = 'Product ' . $request->name . ' was added!';
        }

        //update properties
        $propertyValueIds = [];
        if (isset($request->propertyIds)) {
            foreach ($request->propertyIds as $key => $propertyId) {
                //validate if type of property is "number"
                if ($request->propertyTypes[$key] === 'number') {
                    $this->validate($request, [
                        'propertyValues.'.$key => 'numeric'
                    ],
                    [
                        'propertyValues.'.$key.'.numeric' => 'The property '.Property::find($propertyId)->name.' must be a number',
                    ]);
                }

                $propertyValues = PropertyValue::firstOrCreate(
                    ['value' => $request->propertyValues[$key], 'property_id' => $propertyId]
                );
                $propertyValueIds[] = $propertyValues->id;
            }
        }

        $product->properties()->sync($propertyValueIds);

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
    
    /**
     * @param Request $request
     * @param int $product_id
     * @return RedirectResponse|string
     */
    public function getProperties (Request $request, int $product_id): RedirectResponse|string
    {
        $product = Product::findOrFail($product_id);

        if ($request->ajax()) {
            return view('admin.product-properties', compact('product'))->render();
        }
    
        return '';
    }
}
