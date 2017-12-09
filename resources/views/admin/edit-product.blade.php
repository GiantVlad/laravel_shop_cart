@extends('admin.admin')

@section('left-column')
    @include('admin.admin-left-column')
@stop

@section('content')

    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h3 class="display-3">
                @if ($product) Edit Product
                @else Add New Product
                @endif
            </h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-9">
            <form method="POST" action="{{ url('/admin/products/') }}" enctype="multipart/form-data" class="form-horizontal">
                {{ csrf_field() }}
                @if ($product)
                    <input type="text" name="id" hidden value="{{$product->id}}">
                @endif
                <div class="form-group required">
                    <label for="inputName" class="col-sm-2 control-label">Name:</label>
                    <div class="col-sm-10">
                        <input type="text" name="name" class="form-control" id="inputName" placeholder="Name"
                               @if ($product) value="{{$product->name}}" @endif
                               required minlength="3" maxlength="150">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputDescription" class="col-sm-2 control-label">Description:</label>
                    <div class="col-sm-10">
                        <input type="text" name="description" class="form-control" id="inputDescription"
                               @if ($product) value="{{$product->description}}" @endif
                               placeholder="Description">
                    </div>
                </div>
                <div class="form-group required">
                    <label for="category" class="col-sm-2 control-label">Category:</label>
                    <div class="col-sm-5">
                        <select class="form-control" id="category" name="category" required>
                            <option @if (!$product) selected @endif value="">Select category</option>
                            @foreach( $categories as $category )
                                <option value="{{$category->id}}"
                                    @if ( $product && ($category->id == $product->catalog_id) ) selected @endif>
                                    {{$category->name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <label for="inputPrice" class="col-sm-2 control-label">Price:</label>
                    <div class="col-sm-3">
                        <input required type="text" name="price" class="form-control" id="inputPrice"
                               @if ($product) value="{{$product->price}}" @endif
                               placeholder="Price" minlength="1" maxlength="10">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputFile" class="col-sm-2 control-label">Select Image</label>
                    <div class="col-sm-10">
                        <div class="input-group">
                            <label class="input-group-btn">
                                <span class="btn btn-primary">
                                    Browse&hellip; <input type="file" accept="image/*" data-preview="#preview" name="image" id="inputFile" style="display: none;">
                                </span>
                            </label>
                            <input type="text" class="form-control" readonly>
                        </div>
                        <span class="help-block">
                            The image must be jpeg/jpg/gif/png/svg less than 2Mb
                        </span>
                        <img id="blah" src="@if ($product) {{ url('images/'.$product->image) }} @else # @endif" alt="image" class="img-thumbnail" alt="Product image" width="200">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button class="btn btn-default" type="submit">
                            @if ($product) save
                            @else add Product
                            @endif
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop