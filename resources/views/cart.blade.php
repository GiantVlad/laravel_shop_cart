@extends('app')

@section('content')
    <div class="jumbotron jumbotron-fluid">
        <h3 class="display-3">Product Cart</h3>
    </div>

    <div class="product-form">
        <form name="productCart" class="form-horizontal" action="{{route('post.checkout')}}" method="POST"
              data-toggle="validator">
            @csrf
            @include('layouts.error')

            @if(!empty($products))
                <cart :products="{{json_encode($products)}}" :shipping="{{json_encode($shippingMethods)}}"></cart>
            @endif


            @if (isset($relatedProduct))
                @include('related')
            @endif
        </form>
    </div>
@stop
