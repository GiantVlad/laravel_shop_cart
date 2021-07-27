@extends('app')

@section('content')
    <orders-list
            :orders="{{ json_encode($orders) }}">
    </orders-list>
@stop
