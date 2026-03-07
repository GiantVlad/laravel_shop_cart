<?php

namespace App\Http\Controllers;

use App\Property;
use App\PropertyValue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Product;
use App\Catalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdminProductsController extends Controller
{
    public function __construct ()
    {
        $this->middleware('auth:admin');
    }

    public function list(): Response
    {
        //todo app settings
        $products = Product::with('catalogs')
            ->paginate(15)
            ->through(static function (Product $product): array {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'imageUrl' => $product->image ? url('images/' . $product->image) : null,
                    'categoryName' => $product->catalogs?->name,
                ];
            });
        $categories = Catalog::all('id', 'name');
        
        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'categories' => $categories->map(static fn (Catalog $category): array => [
                'id' => $category->id,
                'name' => $category->name,
            ]),
            'selectedCategoryId' => null,
        ]);
    }
    
    /**
     * @param Request $request
     * @param int|null $category_id
     * @return View|string
     */
    public function categoryFilter(Request $request, ?int $category_id): Response|JsonResponse
    {
        $products = $category_id ?
            Product::with('catalogs')->where('catalog_id', $category_id)->paginate(10) :
            Product::with('catalogs')->paginate(10);

        $categories = Catalog::all('id', 'name');
        $products->through(static function (Product $product): array {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'imageUrl' => $product->image ? url('images/' . $product->image) : null,
                'categoryName' => $product->catalogs?->name,
            ];
        });

        if ($request->expectsJson()) {
            return response()->json(['products' => $products]);
        }

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'categories' => $categories->map(static fn (Catalog $category): array => [
                'id' => $category->id,
                'name' => $category->name,
            ]),
            'selectedCategoryId' => $category_id,
        ]);
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
    public function showEditForm(?int $id = null): Response|RedirectResponse
    {
        $categories = Catalog::all('id', 'name');
        $product = null;
        if ($id) {
            /** @var Product $product */
            $product = Product::with('properties')->findOrFail($id);
            if (!$product instanceof Product) {
                return back()->withErrors('Server Error... Product not found');
            }
        }
        
        /** @var PropertyValue $property */
        if ($product instanceof Product) {
            foreach ($product->properties as $property) {
                if ($property->properties->type === 'selector') {
                    $property->properties->selectProperties =
                        PropertyValue::where('property_id', $property->properties->id)->pluck('value', 'id');
                }
            }
        }

        return Inertia::render('Admin/Products/Edit', [
            'product' => $product ? [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'categoryId' => $product->catalog_id,
                'imageUrl' => $product->image ? url('images/' . $product->image) : null,
                'properties' => $product->properties->map(static function (PropertyValue $property): array {
                    return [
                        'id' => $property->id,
                        'value' => $property->value,
                        'propertyId' => $property->properties->id,
                        'propertyName' => $property->properties->name,
                        'propertyType' => $property->properties->type,
                        'options' => collect($property->properties->selectProperties ?? [])->map(
                            static fn (string $label, int|string $id): array => ['id' => (int)$id, 'value' => $label]
                        )->values(),
                    ];
                })->values(),
            ] : null,
            'categories' => $categories->map(static fn (Catalog $category): array => [
                'id' => $category->id,
                'name' => $category->name,
            ]),
        ]);
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

        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->catalog_id = $request->category;

        if ($request->hasFile('image')) {
            /** @var UploadedFile $image */
            $image = $request->file('image');
            $imageName = 'prod_' . time() . '.' . $image->getClientOriginalExtension();
            $imageDestinationPath = public_path('images/products/');
            $image->move($imageDestinationPath, $imageName);
            $product->image = 'products/' . $imageName;
        }
        //todo image resize

        $product->save();

        $propertyValueIds = [];
        if (isset($request->propertyIds)) {
            foreach ($request->propertyIds as $key => $propertyId) {
                /** @var Property|null $property */
                $property = Property::find($propertyId);

                if (!$property instanceof Property) {
                    continue;
                }

                if ($request->propertyTypes[$key] === Property::TYPE_NUMBER) {
                    $this->validate($request, [
                        'propertyValues.'.$key => 'numeric'
                    ],
                    [
                        'propertyValues.' . $key . '.numeric' => 'The property ' . $property->name . ' must be a number',
                    ]);

                    $propertyValues = PropertyValue::firstOrCreate(
                        ['value' => $request->propertyValues[$key], 'property_id' => $propertyId]
                    );
                } else {
                    $propertyValues = PropertyValue::where('property_id', $propertyId)
                        ->findOrFail((int)$request->propertyValues[$key]);
                }

                $propertyValueIds[] = $propertyValues->id;
            }
        }

        $product->properties()->sync($propertyValueIds);

        return redirect(url('admin/products'))->with('message', $message);
    }
    
    /**
     * @param Request $request
     * @param int $product_id
     * @return RedirectResponse|string
     */
    public function getProperties (Request $request, int $product_id): RedirectResponse|JsonResponse|string
    {
        $product = Product::with('properties.properties')->findOrFail($product_id);

        if ($request->expectsJson()) {
            return response()->json([
                'properties' => $product->properties->map(static function (PropertyValue $property): array {
                    $options = $property->properties->type === Property::TYPE_SELECTOR
                        ? PropertyValue::where('property_id', $property->properties->id)
                            ->get(['id', 'value'])
                            ->map(static fn (PropertyValue $value): array => [
                                'id' => $value->id,
                                'value' => $value->value,
                            ])
                            ->values()
                        : [];

                    return [
                        'id' => $property->id,
                        'value' => $property->value,
                        'propertyId' => $property->properties->id,
                        'propertyName' => $property->properties->name,
                        'propertyType' => $property->properties->type,
                        'options' => $options,
                    ];
                })->values(),
            ]);
        }
    
        return '';
    }
}
