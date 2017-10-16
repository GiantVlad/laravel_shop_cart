<hr/>
<div class="container">
    <h3 class="text-center">We also recommend:</h3>
    <div class="row">
        <input type="hidden" name="related_product_id" value="{{$relatedProduct['related_product_id']}}">

        <div class="col-2">
            <a href="#">
                <img class="img-thumbnail" width="304" height="236"
                     src="{{asset('images/'.$relatedProduct['image']) }}">
            </a>
        </div>
        <div class="col-5">
            <h4>{{$relatedProduct['name']}}</h4>
            <p>{{$relatedProduct['description']}}</p>
        </div>

        <div class="col-2"><p>Price: {{ $relatedProduct['price'] }}</p></div>
        <div class="col-2">
            <button type="submit" name="addRelated" value="addRelated" class="btn btn-primary">Add to Cart
            </button>
        </div>
    </div>
</div>
