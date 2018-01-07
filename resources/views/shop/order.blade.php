@extends('app')


@section('content')

    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h3 class="display-3">Order #{{$order->order_label}}</h3>
        </div>
    </div>
    <div class="row orders-list">
        <div class="row" style="margin-bottom: 5px;">
            <div class="col-md-2"><strong>ID</strong></div>
            <div class="col-md-2"><strong>Created</strong></div>
            <div class="col-md-2"><strong>Total</strong></div>
            <div class="col-md-2"><strong>Status</strong></div>
        </div>
        <div class="row" style="margin-bottom: 5px;">
            <div class="col-md-2">{{$order->order_label}}</div>
            <div class="col-md-2">{{$order->created_at->format('Y-M-d h:i')}}</div>
            <div class="col-md-2">{{$order->total}}</div>
            <div class="col-md-2">{{$order->status}}</div>
        </div>

        <div class="row" style="margin-bottom: 5px;">
        <table class="table table-striped">
            <thead>
            <tr>
                <th class="col-sm-8">Product</th>
                <th class="col-sm-2">Price</th>
                <th class="col-sm-2">QTY</th>
            </tr>
            </thead>
            <tbody>
            @foreach($order->orderData as $orderRow)
            <tr>
                <td><a href="{{ route('product', ['id' => $orderRow->product_id]) }}">{{$orderRow->product->name}}</a></td>
                <td>{{$orderRow->price}}</td>
                <td>{{$orderRow->qty}}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
@stop