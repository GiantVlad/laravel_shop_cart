@extends('app')

@section('content')
    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h3 class="display-3">{{ $product->name }}</h3>
        </div>
    </div>

        <div class="row">
            <div class="col-md-4">
                <form method="post" action="/cart/add-to-cart">
                    @include('layouts.error')
                    <p>{{ $product->description }}</p>
                    <p>Price: <span id="single-price">{{ $product->price }}</span></p>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="productId" value="{{ $product->id }}">
                    <div class="form-group">
                        <label class="control-label" for="productQty">QTY</label>
                        <input type="number" class="form-control" name="productQty"
                               id="single-productQty" placeholder="QTY" value="1"
                               min="1" max="99" required>
                    </div>
                    <div>Total: <span id="single-total"></span></div>
                    <button class="btn btn-primary" type="submit">ADD TO CART</button>
                </form>
            </div>
            <div class="col-md-6">
                <img class="img-thumbnail" alt="product id {{ $product->id }}"
                     width="400" height="300"
                     src="{{ asset('images/'.$product->image) }}">
            </div>
        </div>
    @foreach($product->properties as $property)
    <div class="row">
        <div class="col-md-5">
            {{$property->properties->name}}&nbsp;:&nbsp;{{$property->value}}
        </div>
    </div>
    @endforeach
@stop