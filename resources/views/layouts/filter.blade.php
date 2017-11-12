@extends('app')

@section('left-column')
    <h4 class="header">Filters:</h4>
    <p>{{$properties[0]->name}}</p>
    @foreach($properties as $property)
        <div class="checkbox">
            <label><input type="checkbox" id="filter{{$property['id']}}" data-filter="{{$property['id']}}" value="">{{$property['value']}}</label>
        </div>
    @endforeach

@stop