<hr/>

    <h3 class="text-center">We also recommend:</h3>
    <div class="row">
        <div class="col-md-2">
            <a href="#">
                <img class="img-thumbnail" width="304" height="236"
                     src="{{asset('images/'.$relatedProduct['image']) }}">
            </a>
        </div>
        <div class="col-md-5">
            <h4>{{$relatedProduct['name']}}</h4>
            <p>{{$relatedProduct['description']}}</p>
        </div>

        <div class="col-md-2"><p>Price: {{ $relatedProduct['price'] }}</p></div>
        <div class="col-md-2">
            <button type="button" id="addRelated" value="{{$relatedProduct['id']}}" class="btn btn-primary">Add to Cart
            </button>
        </div>
    </div>
