@extends('app')

@section('left-column')
    <h4 class="header">Filters:</h4>
    @foreach($properties as $property)
        <product-filter :property="{{$property}}"></product-filter>
    @endforeach
@stop