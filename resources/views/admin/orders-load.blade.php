<div class="row" style="margin-bottom: 5px;">
    <div class="col-md-1">ID</div>
    <div class="col-md-1">Created</div>
    <div class="col-md-2">User</div>
    <div class="col-md-2">E-mail</div>
    <div class="col-md-2">Total</div>
    <div class="col-md-2">Commentary</div>
    <div class="col-md-2">Status</div>
</div>
@if (!empty($orders))
    @foreach ($orders as $order)
        <div class="row" style="margin-bottom: 5px;">
            <div class="col-md-1">{{$order->id}}</div>
            <div class="col-md-1">{{$order->created}}</div>
            <div class="col-md-2">{{$order->user->name}}</div>
            <div class="col-md-2">{{$order->user->email}}</div>
            <div class="col-md-1">{{$order->total}}</div>
            <div class="col-md-2">{{$order->commentary}}</div>
            <div class="col-md-1">{{$order->status}}</div>
            <div class="col-md-1"><a href="#">show</a></div>
            <div class="col-md-1"><a href="#">complete</a></div>
            <div class="col-md-1"><a href="#">undo</a></div>
        </div>
    @endforeach
    <div class="users-pagination">
        {{ $orders->links() }}
    </div>
@endif
