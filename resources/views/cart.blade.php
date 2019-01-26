@extends('app')

@section('content')
    <div class="jumbotron jumbotron-fluid">
        <h3 class="display-3">Product Cart</h3>
        <p class="lead">It's a product cart.</p>
    </div>

    <div class="product-form">
        <form name="productCart" class="form-horizontal" action="{{route('post.checkout')}}" method="POST"
              data-toggle="validator">

            @include('layouts.error')
            @csrf
            @if(!empty($products))
                @foreach ($products as $product)
                    @include('product')
                @endforeach
            @endif
            <div class="row">
                <div class="col-md-4 col-md-offset-7">
                    <div class="form-group">
                        <label for="sel1">Select shipping method: </label>
                        <select class="form-control" id="shipping-select">
                            @if (empty($shippingMethods->selected))
                                <option id="empty-option">Select shipping method...</option>
                            @endif
                            @foreach($shippingMethods as $shippingMethod)
                                <option value="{{$shippingMethod->id}}" data-rate="{{$shippingMethod->rate}}"
                                @if ($shippingMethod->id == $shippingMethods->selected)
                                selected="selected" @endif>
                                    {{$shippingMethod->label . ', '. $shippingMethod->time. ', '. $shippingMethod->rate}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10">
                    <p class="text-right">Subtotal: <span id="subtotal"></span></p>
                    <input type="hidden" name="subtotal" value="">
                </div>
                <div class="col-md-2">
                    <button type="submit" id="checkout" class="btn btn-primary" disabled="disabled">Pay</button>
                </div>
            </div>

            @if (isset($relatedProduct))
                @include('related')
            @endif
        </form>
    </div>
@stop
