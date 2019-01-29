@extends('app')


@section('content')

    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h3 class="display-3">Shop</h3>
        </div>
    </div>
    <div class="row product-list">
        @foreach ($products as $product)
            <product :product="{{ $product }}"></product>
        @endforeach
    </div>

@stop