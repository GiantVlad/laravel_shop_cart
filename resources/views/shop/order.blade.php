@extends('app')

@section('content')
    <order-info order-id='{{ $orderId }}' product-route='{{ route('product', false) }}'>
    </order-info>
@stop
