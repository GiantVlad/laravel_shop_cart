@php $i=0; @endphp
@foreach ($products as $product)
    @php $i++; @endphp
    <div class="row" style="margin-bottom: 5px;">
        <div class="col-md-1">{{$i}}</div>
        <div class="col-md-1">
            <img src="{{ url('images/'.$product->image) }}" class="img-thumbnail" alt="image" width="152" height="118">
        </div>
        <div class="col-md-2">{{$product->name}}</div>
        <div class="col-md-3">{{$product->description}}</div>
        <div class="col-md-1">{{$product->price}}</div>
        <div class="col-md-2">{{$product->catalogs->name}}</div>
        <div class="col-md-1"><a href="{{ url('/admin/edit-product/'.$product->id) }}">edit</a></div>
        <div class="col-md-1">
            <form method="POST" action="{{ route('product.delete') }}">
                <input type="hidden" name="id" value="{{ $product->id }}">
                {{ method_field('DELETE') }}
                {{ csrf_field() }}
                <button class="btn btn-xs btn-default"  type="submit" data-toggle="confirmation-singleton">
                    remove
                </button>
            </form>
        </div>
    </div>
@endforeach
<div class="products-pagination">
{{ $products->links() }}
</div>
