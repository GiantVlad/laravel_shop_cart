@extends('app')
@section('content')
    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h3 class="display-3">Shop</h3>
        </div>
    </div>
    <div class="row product-list">
        <product-list :keyword="{{json_encode($keyword ?? '')}}"></product-list>
    </div>
@stop
