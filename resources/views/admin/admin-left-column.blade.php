@extends('admin.admin')

@section('left-column')
    <div class="row">
        <div><a href="{{ route('admin.categories') }}">Categories</a></div>
        <div><a href="{{ route('admin.products') }}">Products</a></div>
        <div><a href="{{ route('admin.users') }}">Users</a></div>
        <div><a href="{{ route('admin.orders') }}">Orders</a></div>
        <div>Payment</div>
        <div><a href="{{ route('admin.shipping-methods') }}">Shipping</a></div>
        <div>Settings</div>
    </div>

@stop