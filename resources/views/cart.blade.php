@extends('app')

@section('content')
    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h3 class="display-3">Product Cart</h3>
            <p class="lead">It's a product cart.</p>
        </div>
    </div>
    <div class="product-form">
        <form name="productCart" class="form-horizontal" action="/cart" method="POST" data-toggle="validator">
            <div class="container">
                @include('layouts.error')
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                @foreach ($products as $product)
                    @include('product')
                @endforeach

                <div class="row">
                    <div class="col-md-10">
                        <p class="text-right">Subtotal: <span id="subtotal"></span></p>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Pay</button>
                    </div>
                </div>
            </div>
            @if (isset($relatedProduct))
                @include('related')
            @endif
        </form>
    </div>
@stop
