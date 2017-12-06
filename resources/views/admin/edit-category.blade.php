@extends('admin.admin')

@section('left-column')
    @include('admin.admin-left-column')
@stop

@section('content')

    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h3 class="display-3">Add New Category</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-9">
            <form method="POST" action="{{ url('/admin/categories/') }}" class="form-horizontal">
                {{ csrf_field() }}
                <div class="form-group required">
                    <label for="inputName" class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10">
                        <input type="text" name="name" class="form-control" id="inputName" placeholder="Name" required minlength="3" maxlength="30">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputDescription" class="col-sm-2 control-label">Description</label>
                    <div class="col-sm-10">
                        <input type="text" name="description" class="form-control" id="inputDescription"
                               placeholder="Description">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPriority" class="col-sm-2 control-label">Priority</label>
                    <div class="col-sm-2">
                        <input type="number" name="priority" class="form-control" id="inputPriority" max="99">
                    </div>

                    <label for="parent" class="col-sm-2 control-label">Parent</label>
                    <div class="col-sm-6">
                        <select class="form-control" id="parent" name="parent">
                            <option selected value="">No parent</option>
                            @foreach( $categories_names as $cat_name )
                                <option value="{{$cat_name->id}}">{{$cat_name->name}}</option>
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
                                    Browse&hellip; <input type="file" name="image" id="inputFile" style="display: none;">
                                </span>
                            </label>
                            <input type="text" class="form-control" readonly>
                        </div>
                        <span class="help-block">
                            Try selecting one file and watch the feedback
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button class="btn btn-default" type="submit">
                            add category
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop