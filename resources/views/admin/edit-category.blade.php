@extends('admin.admin')

@section('left-column')
    @include('admin.admin-left-column')
@stop

@section('content')

    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h3 class="display-3">
                @if ($category) Edit Category
                @else Add New Category
                @endif
            </h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-9">
            <form method="POST" action="{{ url('/admin/categories/') }}" enctype="multipart/form-data" class="form-horizontal">
                {{ csrf_field() }}
                @if ($category)
                    <input type="hidden" name="id" value="{{$category->id}}">
                @endif
                <div class="form-group required">
                    <label for="inputName" class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10">
                        <input type="text" name="name" class="form-control" id="inputName" placeholder="Name"
                               @if ($category) value="{{$category->name}}" @endif
                               required minlength="3" maxlength="30">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputDescription" class="col-sm-2 control-label">Description</label>
                    <div class="col-sm-10">
                        <input type="text" name="description" class="form-control" id="inputDescription"
                               @if ($category) value="{{$category->description}}" @endif
                               placeholder="Description">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPriority" class="col-sm-2 control-label">Priority</label>
                    <div class="col-sm-2">
                        <input type="number" name="priority" class="form-control" id="inputPriority"
                               @if ($category) value="{{$category->priority}}" @endif max="99">
                    </div>

                    <label for="parent" class="col-sm-2 control-label">Parent</label>
                    <div class="col-sm-6">
                        <select class="form-control" id="parent" name="parent">
                            <option @if (!$category) selected @endif value="">No parent</option>
                            @foreach( $parent_categories_names as $par_cat )
                                <option value="{{$par_cat->id}}"
                                    @if ( $category && ($par_cat->id == $category->parent_id) ) selected @endif>
                                    {{$par_cat->name}}
                                </option>
                            @endforeach
                        </select>
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
                        <img id="blah" src="@if ($category) {{ url($category->image) }} @else # @endif" alt="image" class="img-thumbnail" alt="Category image" width="200">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button class="btn btn-default" type="submit">
                            @if ($category) save
                            @else add category
                            @endif
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop