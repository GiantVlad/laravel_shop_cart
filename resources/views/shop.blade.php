@extends('app')


@section('content')

    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h3 class="display-3">Shop</h3>
        </div>
    </div>
    <div class="row product-list">
        @foreach ($products as $product)
            <div class="col-md-4 col-sm-6 product-cart">
                <div class="cart-wrapper">
                    <div class="cart-header">
                        <a href="{{ asset('shop/'.$product->id) }}">
                            <h4 class="header">{{$product->name}}</h4>
                        </a>
                        <p>Catalog: {{$product->catalogs->name}}</p>
                        <p>Property:
                            @foreach ($product->properties as $productProperty)
                                {{$productProperty->value}}<br/>
                            @endforeach
                        </p>
                        <div class="effect">
                            <div class="spacer">
                                <span class="glyphicon glyphicon-triangle-bottom"></span>
                            </div>
                        </div>
                    </div>
                    <div class="thumbnail">
                        <a href="{{ asset('shop/'.$product->id) }}">
                            <div class="img-wrapper">
                                <img class="center-block" alt="product id {{ $product->id }}"
                                     src="{{ asset('images/'.$product->image) }}">
                            </div>
                        </a>
                    </div>
                    <p>Price: {{$product->price}}
                        <button class="btn btn-link" name="addFromShop{{ $product->id }}" value="{{ $product->id }}">
                            ADD TO CART
                        </button>
                    </p>
                </div>
            </div>

        @endforeach
    </div>

@stop