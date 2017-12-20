<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        {{--<input type="text" hidden id="modal-product-id">--}}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Create a new product property</h4>
        </div>
        <div class="modal-body" id="property-modal-body">
            <div class="row">
                <div class="col-sm-5">
                    <div class="form-group">
                        <label for="new-property-name">Property name:</label>
                        <input type="text" class="form-control" id="new-property-name" required minlength="2" maxlength="50">
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="new-property-priority">Priority:</label>
                        <input type="number" class="form-control" id="new-property-priority" max="999">
                    </div>
                </div>
                <div class="col-sm-5" id="propertyType">
                    <label>Property Type:</label>
                    <div class="form-group">
                        <label class="radio-inline"><input type="radio" name="new-property-type" checked value="selector">selector</label>
                        <label class="radio-inline"><input type="radio" name="new-property-type" value="number">number</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="new-property-value">Property value:</label>
                        <input type="text" class="form-control" id="new-property-value" required minlength="2" maxlength="150">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" id="save-new-property-modal-button">
                SAVE
            </button>
        </div>
    </div>
</div>