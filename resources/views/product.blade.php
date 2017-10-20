<div class="product-row">
    <div class="row">
        <input type="hidden" name="productId[]" value="{{$product['id']}}">
        <div class="col-2"><a href="#"><img class="img-thumbnail" alt="product id {{ $product['id'] }}"
                                            width="304" height="236"
                                            src="{{ asset('images/'.$product['image']) }}"></a>
        </div>
        <div class="col-5">
            <h4>{{$product['name']}}</h4>
            <p>{{$product['description']}}</p>
            <button class="btn btn-link" name="delete{{$product['id']}}" value="{{$product['id']}}">Remove
            </button>
        </div>
        <div class="col-2 form-group">
            <label class="control-label" for="productQty{{$product['id']}}">QTY</label>
            <input type="text" class="form-control" name="productQty[]"
                   id="productQty{{$product['id']}}" placeholder="QTY" value="{{$product['qty']}}"
                   min="1" max="99" required>
        </div>
        <div class="col-1"><p>Price: <span id="price{{$product['id']}}">{{$product['price']}}</span></p>
        </div>
        <div class="col-1"><p>Total: <span id="total{{$product['id']}}"></span></p></div>
        <input type="hidden" name="isRelatedProduct[]" value="{{ $product['is_related'] }}">
    </div>
    <hr/>
</div>
