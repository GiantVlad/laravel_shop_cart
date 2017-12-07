@extends('admin.admin')

@section('left-column')
    @include('admin.admin-left-column')
@stop

@section('content')

    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h3 class="display-3">Categories</h3>
            <a class="btn bg-primary" href="{{route('add-category')}}">Add New Category</a>
        </div>
    </div>

    @php $i=0; @endphp
    @foreach ($categories as $category)
        @php $i++; @endphp
        <div class="row" style="margin-bottom: 5px;">
            <div class="col-md-1">{{$i}}</div>
            <div class="col-md-1">
                <img src="{{ url($category->image) }}" class="img-thumbnail" alt="image" width="152" height="118">
            </div>
            <div class="col-md-2">{{$category->name}}</div>
            <div class="col-md-1">{{$category->priority}}</div>
            <div class="col-md-2">{{$category->parent_name}}</div>
            <div class="col-md-1"><a href="{{ url('/admin/edit-category/'.$category->id) }}">edit</a></div>
            <div class="col-md-1">
                <form method="POST" action="{{ url('/admin/categories/'.$category->id) }}">
                    <input type="hidden" name="id" value="{{ $category->id }}">
                    {{ method_field('DELETE') }}
                    {{ csrf_field() }}
                    <button class="btn btn-xs btn-default"  type="submit" data-toggle="confirmation-singleton">
                        remove
                    </button>
                </form>
            </div>
        </div>
    @endforeach

@stop