@extends('app')

@section('content')
    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h3 class="display-3">{{ $product->name }}</h3>
        </div>
    </div>

    <div id="app">
        <single-item :item-data="{{json_encode($product)}}"></single-item>
    </div>
@stop