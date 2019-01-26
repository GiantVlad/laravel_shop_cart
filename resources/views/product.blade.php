<div class="product-row">
    <div class="row">
        <input type="hidden" name="productId[]" value="{{$product['id']}}">
        <div class="col-md-2"><a href=" {{ route('product', $product['id']) }}"><img class="img-thumbnail" alt="product id {{ $product['id'] }}"
                                            height="240"
                                            src="{{ asset('images/'.$product['image']) }}"></a>
        </div>
        <div class="col-md-5">
            <h4>{{$product['name']}}</h4>
            <p>{{$product['description']}}</p>
            <button class="btn btn-link" name="delete{{$product['id']}}" value="{{$product['id']}}">Remove
            </button>
        </div>
        <div class="col-md-2 form-group">
            <label class="control-label" for="productQty{{$product['id']}}">QTY</label>
            <input type="number" class="form-control" name="productQty[]"
                   id="productQty{{$product['id']}}" placeholder="QTY" value="{{$product['qty']}}"
                   min="1" max="99" required>
        </div>
        <div class="col-md-1"><p>Price: <span id="price{{$product['id']}}">{{$product['price']}}</span></p>
        </div>
        <div class="col-md-1"><p>Total: <span id="total{{$product['id']}}"></span></p></div>
        <input type="hidden" name="isRelatedProduct[]" value="{{ $product['is_related'] }}">
    </div>
    <hr/>
</div>
