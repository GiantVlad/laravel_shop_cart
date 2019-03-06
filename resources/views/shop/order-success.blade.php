

@extends('app')

@section('content')
<div class="container">
    <div class="row">
        <a href="{{route('shop')}}" class="btn btn-primary">Back to the shop</a>
        <a href="{{route('orders')}}" class="btn btn-primary">To the orders</a>
    </div>
</div>
@stop