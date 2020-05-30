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

        if ($propertyValues->isEmpty()) $propertyValues = ['property_id' => $id, 'type' => 'number'];

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
    public function createProperty(Request $request)
    {
        $this->validate($request, [
            'property_name' => 'required | min:2 | max:50',
            'property_priority' => 'max:999',
            'property_type' => 'required | min:2 | max:30',
            'property_value' => 'min:2 | max:150'
        ]);
        $property = Property::firstOrCreate(
            ['name' => $request->property_name], ['priority' => $request->property_priority, 'type' => $request->property_type]
        );
        if ($property->id && $request->property_value) {
            //ToDo Units
            PropertyValue::firstOrCreate(
                ['value' => $request->property_value, 'property_id' => $property->id], [ 'unit_id' => null ]
            );
        }

        if ($request->ajax()) {
            return $property->id;
        }
    }
}
