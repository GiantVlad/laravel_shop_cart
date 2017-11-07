@extends('app')


@section('content')

    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h3 class="display-3">Shop</h3>
        </div>
    </div>
    <div class="container">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="row">
            @foreach ($products as $product)

                <div class="col-md-4 col-sm-6">
                    <h5>{{$product->name}}</h5>
                    <div class="thumbnail">
                        <a href="{{ asset('shop/'.$product->id) }}">
                            <div class="img-wrapper">
                                <img class="center-block" alt="product id {{ $product->id }}"
                                     height="300"
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

            @endforeach
        </div>
    </div>

@stop