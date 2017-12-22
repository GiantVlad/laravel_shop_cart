@extends('admin.admin')

@section('left-column')
    @include('admin.admin-left-column')
@stop

@section('content')
    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h3 class="display-3">Products</h3>
            <a class="btn bg-primary" href="{{route('add-product')}}">Add New Product</a>
        </div>
    </div>
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-sm-4 col-lg-offset-1">
            <div class="input-group">
            <input type="text" class="form-control" placeholder="Search by name" id="nav-search">
            <div class="input-group-btn">
                <button class="btn btn-default" type="button" id="nav-search-btn">
                    <i class="glyphicon glyphicon-search"></i>
                </button>
            </div>
        </div>
        </div>
        <div class="col-sm-4">
            <select class="form-control" id="product-category" name="category">
                <option selected value="">Select category</option>
                @foreach( $categories as $category )
                    <option value="{{$category->id}}">
                        {{$category->name}}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="product-list">
        @include('admin.products-load')
    </div>
@stop