@extends('app')

@section('left-column')
    <product-filters :properties="{{$properties}}"></product-filters>
@stop
