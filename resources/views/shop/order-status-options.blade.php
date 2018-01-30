<option>select action ..</option>
@if ($order->status !== 'pending payment')
    <option value="repeat" data-order-id="{{$order->id}}">repeat order</option>
@else
    <option value="replay payment" data-order-id="{{$order->id}}">replay payment</option>
    <option value="deleted" data-order-id="{{$order->id}}">undo order</option>
@endif