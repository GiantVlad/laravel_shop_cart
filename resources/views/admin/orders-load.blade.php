<div class="row" style="margin-bottom: 5px;">
    <div class="col-md-2">ID</div>
    <div class="col-md-2">Created</div>
    <div class="col-md-2">User/E-mail</div>
    <div class="col-md-1">Total</div>
    <div class="col-md-2">Status</div>
</div>
@if (!empty($orders))
    @foreach ($orders as $order)
        <div class="row" style="margin-bottom: 5px;">
            <div class="col-md-2">{{$order->order_label}}</div>
            <div class="col-md-2">{{$order->created_at->format('Y-M-d h:i')}}</div>
            <div class="col-md-2">{{$order->user->name}}<br>{{$order->user->email}}</div>
            <div class="col-md-1">{{$order->total}}</div>
            <div class="col-md-2">{{$order->status}}</div>
            <div class="col-md-1"><a href="#">show</a></div>
            <div class="col-md-1"><a href="#">complete</a></div>
            <div class="col-md-1"><a href="#">undo</a></div>
        </div>
    @endforeach
    <div class="users-pagination">
        {{ $orders->links() }}
    </div>
@endif
