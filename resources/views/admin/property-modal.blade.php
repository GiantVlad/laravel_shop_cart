<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <input type="text" hidden id="modal-product-id">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Add property to Product</h4>
        </div>
        <div class="modal-body" id="property-modal-body">
            <div class="row">
                <div class="col-sm-6">
                    <select class="selectpicker" data-live-search="true" id="propertyType" data-input-type="">
                        @foreach ($properties as $propertyType)
                            <option value="{{$propertyType->id}}">{{$propertyType->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6" id="propertyValues">
                    {{-- select inclided by AJAX --}}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" id="add-property-modal-button">
                Add property
            </button>
        </div>
    </div>
</div>