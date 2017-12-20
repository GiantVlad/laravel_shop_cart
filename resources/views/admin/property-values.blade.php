@if (is_array($propertyValues))
    <input type="number" class="form-control" id="propertyTypeValue" value="" data-input-type="{{$propertyValues['type']}}"
           data-property-id="{{$propertyValues['property_id']}}">

@else
    @if ($propertyValues->first()->properties->type === 'selector')
        <select class="form-control" id="propertyTypeValue" data-input-type="{{$propertyValues->first()->properties->type}}"
                data-property-id="{{$propertyValues->first()->properties->id}}">

            @foreach ($propertyValues as $propertyVal)
                <option value="{{$propertyVal->id}}">{{$propertyVal->value}}</option>
            @endforeach

        </select>
    @else
        <input type="number" class="form-control" id="propertyTypeValue" value="" data-input-type="{{$propertyValues->first()->properties->type}}"
               data-property-id="{{$propertyValues->first()->properties->id}}">
    @endif
@endif