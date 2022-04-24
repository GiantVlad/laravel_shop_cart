@extends('app')

@section('content')
    <div class="jumbotron jumbotron-fluid">
        <h3 class="display-3">Product Cart</h3>
    </div>

    <div class="product-form">
        <form name="productCart" class="form-horizontal">
            @include('layouts.error')

            @if(!empty($products))
                <cart
                    :products="{{ json_encode($products) }}"
                    :shipping="{{ json_encode($shippingMethods) }}"
                    :payments="{{ json_encode($payments) }}"
                    @if (!empty($relatedProduct))
                        :related-product="{{json_encode($relatedProduct)}}"
                    @else
                        :related-product="{}"
                    @endif
                    >
                </cart>
            @endif

        </form>
    </div>
@stop
