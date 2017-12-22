<div class="row" style="margin-bottom: 5px;">
    <div class="col-md-2">Label</div>
    <div class="col-md-1">priority</div>
    <div class="col-md-1">Status</div>
    <div class="col-md-1"></div>
    <div class="col-md-1"></div>
</div>
@if (!empty($shippingMethods))
    @foreach ($shippingMethods as $shippingMethod)
        <div class="row" style="margin-bottom: 5px;">
            <div class="col-md-2">{{$shippingMethod->label}}</div>
            <div class="col-md-1">{{$shippingMethod->priority}}</div>
            <div class="col-md-1">{{$shippingMethod->enable ? 'enable' : 'disable'}}</div>
            <div class="col-md-1"><a href="#">edit</a></div>
            <div class="col-md-1">
                <a class="btn btn-sm btn-default shipping-method-change-status" href="#"
                    data-status="{{$shippingMethod->enable}}" data-method-id="{{$shippingMethod->id}}">
                </a>
            </div>
        </div>
    @endforeach
@endif
