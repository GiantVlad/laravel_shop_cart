@extends('admin.admin')

@section('left-column')
    @include('admin.admin-left-column')
@stop

@section('content')

    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h3 class="display-3">
                Edit User
            </h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-9">
            <form method="POST" action="{{ route('user.update') }}" class="form-horizontal">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{$user->id}}">
                <input type="hidden" name="_method" value="put">
                <div class="form-group required">
                    <label for="inputName" class="col-sm-2 control-label">Name:</label>
                    <div class="col-sm-10">
                        <input type="text" name="name" class="form-control" id="inputName" placeholder="Name"
                               value="{{$user->name}}" required minlength="3" maxlength="150">
                    </div>
                </div>
                <div class="form-group required">
                    <label for="inputEmail" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10">
                        <input type="email" name="email" class="form-control" id="inputEmail"
                               value="{{$user->email}}" required minlength="3" maxlength="150"
                               placeholder="Description">
                    </div>
                </div>


                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button class="btn btn-default" type="submit">
                            save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop