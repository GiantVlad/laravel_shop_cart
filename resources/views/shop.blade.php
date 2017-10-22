@extends('app')

@section('content')
    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h3 class="display-3">Shop</h3>
        </div>
    </div>
    <div class="container">
        <div class="row">
            @foreach ($products as $product)

                <div class="col-md-4 col-sm-6">
                    <h5>{{$product->name}}</h5>
                    <a href="shop/{{$product->id}}"><img class="img-thumbnail" alt="product id {{ $product->id }}"
                            width="400" height="300"
                            src="{{ asset('images/'.$product->image) }}">
                    </a>

                    <p>Price: {{$product->price}} <a href="#"><span>ADD TO CART</span></a></p>
                </div>

            @endforeach
        </div>
    </div>

@stop