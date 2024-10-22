@extends('app_old')

@section('content')
    <orders-list
            :orders="{{ json_encode($orders) }}">
    </orders-list>
@stop
