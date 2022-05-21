@extends('admin.admin')

@section('left-column')
    @include('admin.admin-left-column')
@stop

@section('content')
    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h3 class="display-3">Payment Methods</h3>
        </div>
    </div>

    <div class="shipping-methods-list">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Label</th>
                <th>Key</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Class Name</th>
                <th>Action (ToDo)</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($paymentMethods as $method)
            <tr>
                <td>{{ $method['label'] }}</td>
                <td>{{ $method['configKey'] }}</td>
                <td>{{ $method['status'] }}</td>
                <td>{{ $method['priority'] }}</td>
                <td>{{ $method['className'] }}</td>
                <td>{{ 'enable/disable/addToDB/removeFromDB' }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@stop
