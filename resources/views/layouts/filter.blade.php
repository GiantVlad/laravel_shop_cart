@extends('app')

@section('left-column')
    <h4 class="header">Filters:</h4>
    @foreach($properties as $property)
        <p>{{$property->name}}</p>
        @if ($property->type === 'selector')
            @foreach ($property->propertyValues as $propertyValue)

                <div class="checkbox">
                    <label><input type="checkbox" id="filter{{$propertyValue->id}}" data-filter="{{$propertyValue->id}}"
                                  value="">{{$propertyValue->value}}</label>
                </div>
            @endforeach
        @elseif ($property->type === 'number')
            <div class="form-group">
                <input type="text" id="select-property-min-{{$property->id}}" class="form-control"
                       data-filter="{{$property->id}}" placeholder="min">
            </div>
            <div class="form-group">
                <input type="text" id="select-property-max-{{$property->id}}" class="form-control"
                       data-filter="{{$property->id}}" placeholder="max">
            </div>
        @endif
    @endforeach

@stop