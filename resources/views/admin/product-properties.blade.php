@foreach($product->properties as $property)
    <div class="form-group" id="product-property{{$property->id}}" data-prop-id="{{$property->properties->id}}">
        <input type="text" hidden name="propertyIds[]" readonly value="{{$property->properties->id}}">
        <input type="text" hidden name="propertyTypes[]" readonly value="{{$property->properties->type}}">
        <label for="propertyValue" class="col-sm-2 control-label">{{$property->properties->name}}:</label>
        <div class="col-sm-3">
            @if ($property->properties->selectProperties)
                <select class="form-control" id="product-category" name="propertyValues[]">
                    @foreach( $property->properties->selectProperties as $key => $selectProperty )
                        <option value="{{$key}}" @if ($property->value === $selectProperty) selected @endif>
                            {{$selectProperty}}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="text" class="form-control" name="propertyValues[]" value="{{$property->value}}">
            @endif
        </div>
        <div class="col-sm-1" style="padding-top: 7px;">
            <button class="btn btn-xs btn-default" type="button" id="removeProperty" data-value="{{$property->id}}" data-product_id="{{$product->id}}" data-toggle="confirmation-singleton">
                remove
            </button>
        </div>
    </div>
@endforeach