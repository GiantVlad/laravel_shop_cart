<?php

namespace App\Http\Controllers;

use App\Catalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Property;
use App\Product;

/**
 * Class PropertyController
 * @package App\Http\Controllers
 */
class PropertyController extends Controller
{
    private Catalog $catalog;

    public function __construct (Catalog $catalog)
    {
        $this->catalog = $catalog;
    }
    
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function filter (Request $request)
    {
        //ToDO check is $request->properties correct
        //mapping $request->properties for query

        $category_id = $request->category;
        $category_ids = $this->catalog->getCatalogIdsTree($category_id);

        if (count($request->properties) < 1) {
            $products = Product::whereIn('catalog_id', $category_ids)->get();
        } else {
            $properties = [];
            $property_val_ids = [];
            $conditions = [];

            $index = 0;
            foreach ($request->properties as $property) {
                if (isset($property['property_value_id'])) $property_val_ids[] = $property['property_value_id'];
                if (isset($property['property_id'])) {
                    $conditions[$index] = [];
                    array_push($conditions[$index], ['property_id', '=', $property['property_id']]);
                    if (isset($property['maxValue'])) array_push($conditions[$index], ['value', '<=', $property['maxValue']+0]);
                    if (isset($property['minValue'])) array_push($conditions[$index], ['value', '>=', $property['minValue']+0]);
                    $index++;
                }
            }
            $properties['property_val_ids'] = $property_val_ids;
            $properties['conditions'] = $conditions;

            $products = Product::whereIn('catalog_id', $category_ids)->when($properties, function ($query) use ($properties) {
                $property_val_ids = $properties['property_val_ids'];
                if ($property_val_ids) {
                    $query = $query->whereHas('properties', function ($q) use ($property_val_ids) {
                        $resp = $q->whereIn('property_value_id', $property_val_ids);
                        //todo check it
                        if ($resp)
                            return $resp;
                    });
                }
                $conditions = $properties['conditions'];
                foreach ($conditions as $condition) {
                    $query = $query->whereHas('properties', function ($q) use ($condition) {
                        //todo check it
                        $resp = $q->where($condition);
                        if ($resp)
                            return $resp;
                    });
                }
                return $query;
            })->get();
        }
        if (count($products) > 0) {
            $html = view('shop', compact('products'))->render();
        } else {
            $html = view('layouts.product-not-found', [])->render();
        }
        return response()->json(compact('html'));
    }
}
