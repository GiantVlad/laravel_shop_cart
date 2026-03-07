<?php

namespace App\Http\Controllers;

use App\Property;
use App\PropertyValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminPropertiesController extends Controller
{
    public function __construct ()
    {
        $this->middleware('auth:admin');
    }
    
    /**
     * @param Request $request
     * @return string
     */
    public function getProperties(Request $request): JsonResponse|string
    {
        $properties = Property::all();

        if ($request->expectsJson()) {
            return response()->json([
                'properties' => $properties->map(static fn (Property $property): array => [
                    'id' => $property->id,
                    'name' => $property->name,
                    'type' => $property->type,
                ]),
            ]);
        }
    
        return '';
    }
    
    /**
     * @param Request $request
     * @param int $id
     * @return string
     */
    public function getPropertyValues(Request $request, int $id): JsonResponse|string
    {
        $propertyValues = PropertyValue::where('property_id', $id)->get();

        if ($propertyValues->isEmpty()) {
            $propertyValues = ['property_id' => $id, 'type' => 'number'];
        }

        if ($request->expectsJson()) {
            if (is_array($propertyValues)) {
                return response()->json([
                    'propertyValues' => [],
                    'propertyType' => $propertyValues['type'],
                    'propertyId' => $propertyValues['property_id'],
                ]);
            }

            return response()->json([
                'propertyValues' => $propertyValues->map(static fn (PropertyValue $value): array => [
                    'id' => $value->id,
                    'value' => $value->value,
                ]),
                'propertyType' => Property::TYPE_SELECTOR,
                'propertyId' => $id,
            ]);
        }
    
        return '';
    }
    
    /**
     * @param Request $request
     * @return int
     * @throws ValidationException
     */
    public function addPropertyToProduct(Request $request): JsonResponse|int
    {
        $this->validate($request, [
            'property_value' => 'required',
            'product_id' => 'required|min:1',
            'property_input_type' => 'required',
            'property_id' => 'required',
        ],[
            //ToDO check it's work
            'Server Error... Please try again.',
        ]);

        if ($request->property_input_type === 'number') {
            /** @var PropertyValue $propertyValue */
            $propertyValue = PropertyValue::firstOrCreate(
                ['value' => $request->property_value, 'property_id' => $request->property_id]
            );
        } else {
            /** @var PropertyValue $propertyValue */
            $propertyValue = PropertyValue::find($request->property_value);
        }
        $propertyValue->products()->syncWithoutDetaching([$request->product_id]);
        
        if ($request->expectsJson()) {
            return response()->json(['productId' => (int)$request->get('product_id')]);
        }
    
        return 0;
    }
    
    /**
     * @param Request $request
     * @return int
     * @throws ValidationException
     */
    public function createProperty(Request $request): JsonResponse|int
    {
        $this->validate($request, [
            'property_name' => 'required | min:2 | max:50',
            'property_priority' => 'max:999',
            'property_type' => 'required | min:2 | max:30',
            'property_value' => 'min:2 | max:150'
        ]);
        /** @var Property $property */
        $property = Property::firstOrCreate(
            ['name' => $request->get('property_name')],
            [
                'priority' => $request->get('property_priority'),
                'type' => $request->get('property_type')
            ]
        );
        
        if ($property->id && $request->get('property_value')) {
            //ToDo Units
            PropertyValue::firstOrCreate(
                ['value' => $request->get('property_value'), 'property_id' => $property->id],
                ['unit_id' => null],
            );
        }

        if ($request->expectsJson()) {
            return response()->json(['propertyId' => $property->id]);
        }
    
        return 0;
    }
}
