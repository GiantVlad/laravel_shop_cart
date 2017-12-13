<?php

namespace App\Http\Controllers;

use App\Property;
use App\PropertyValue;
use Illuminate\Http\Request;


class AdminPropertiesController extends Controller
{
    public function __construct ()
    {
        $this->middleware('auth:admin');
    }

    public function getProperties(Request $request)
    {
        $properties = Property::all();

        if ($request->ajax()) {
            return view('admin.property-modal', compact('properties'))->render();
        }
    }

    public function getPropertyValues(Request $request, $id)
    {
        $propertyValues = PropertyValue::where('property_id', $id)->get();

        if ($request->ajax()) {
            return view('admin.property-values', compact('propertyValues'))->render();
        }
    }

    public function addPropertyToProduct(Request $request)
    {
        $this->validate($request, [
            'property_value' => 'required',
            'product_id' => 'required | min:1',
            'property_input_type' => 'required',
            'property_id' => 'required',
        ],[
            //ToDO check it's work
            'Server Error... Please try again.',
        ]);

        if ($request->property_input_type === 'number') {
            $propertyValue = PropertyValue::firstOrCreate(
                ['value' => $request->property_value, 'property_id' => $request->property_id]
            );
        } else {
            $propertyValue = PropertyValue::find($request->property_value);
        }
        $propertyValue->products()->syncWithoutDetaching([$request->product_id]);
        if ($request->ajax()) {
            return $request->product_id;
        }
    }
}
