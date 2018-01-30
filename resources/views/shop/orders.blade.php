@extends('app')


@section('content')

    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h3 class="display-3">Orders</h3>
        </div>
    </div>
    <div class="row orders-list">
        <div class="row" style="margin-bottom: 5px;">
            <div class="col-md-2">ID</div>
            <div class="col-md-2">Created</div>
            <div class="col-md-2">Total</div>
            <div class="col-md-2">Status</div>
        </div>
        @if (!empty($orders))
            @foreach ($orders as $order)
                <div class="row" style="margin-bottom: 5px;">
                    <div class="col-md-2">{{$order->order_label}}</div>
                    <div class="col-md-2">{{$order->created_at->format('Y-M-d h:i')}}</div>
                    <div class="col-md-2">{{$order->total}}</div>
                    <div class="col-md-2 order-status">{{$order->status}}</div>
                    <div class="col-md-1"><a href="{{route('order', ['id' => $order->id])}}">details</a></div>
                    <div class="col-md-2">
                        <select class="form-control" id="order-status-actions" data-order-id="{{$order->id}}">
                            @include('shop.order-status-options')
                        </select>
                    </div>
                </div>
            @endforeach
            <div class="users-pagination">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

@stop