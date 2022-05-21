@extends('admin.admin')

@section('left-column')
    @include('admin.admin-left-column')
@stop

@section('content')
    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h3 class="display-3">Payment Methods</h3>
        </div>
    </div>
    <div class="shipping-methods-list">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Label</th>
                <th>Key</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Class Name</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($paymentMethods as $method)
            <tr>
                <td>{{ $method['label'] }}</td>
                <td>{{ $method['configKey'] }}</td>
                <td>{{ App\Enums\AdminPaymentMethodStatuses::STATUSES[$method['status']] }}</td>
                <td>
                    @if($method['status'] === App\Enums\AdminPaymentMethodStatuses::MISSED_IN_DB)
                        -
                    @else
                        <input class="pm-priority form-control" id="{{ $method['id'] }}" type="number" value="{{ $method['priority'] }}">
                    @endif
                </td>
                <td>{{ $method['className'] }}</td>
                <td>
                @if($method['status'] === App\Enums\AdminPaymentMethodStatuses::DISABLED)
                    <button type="button" class="pm-action btn btn-default btn-sm" data-id="{{ $method['id'] }}" data-type="enable">Enable</button>
                @elseif($method['status'] === App\Enums\AdminPaymentMethodStatuses::ENABLED)
                    <button type="button" class="pm-action btn btn-default btn-sm" data-id="{{ $method['id'] }}" data-type="disable">Disable</button>
                @elseif($method['status'] === App\Enums\AdminPaymentMethodStatuses::MISSED_IN_DB)
                    <button
                            type="button"
                            class="pm-action btn btn-default btn-sm"
                            data-id="{{ $method['id'] }}"
                            data-key="{{ $method['configKey'] }}"
                            data-type="add_to_db"
                    >
                        Add to the DB
                    </button>
                @elseif($method['status'] === App\Enums\AdminPaymentMethodStatuses::MISSED_IN_CFG)
                    <button
                            type="button"
                            class="pm-action btn btn-default btn-sm"
                            data-id="{{ $method['id'] }}"
                            data-type="remove_from_db"
                    >
                        Remove from the DB
                    </button>
                @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <script>
        $(document).ready(function() {
            const baseUrl = document.location.origin
            const token = $('meta[name="csrf-token"]').attr('content')
            $('button.pm-action').click(function (e) {
                const action = $(this).attr('data-type')
                const id = $(this).attr('data-id')
                const payment_key = $(this).attr('data-key')
                $.post(baseUrl + '/admin/payment-method-action/' + id, {
                    action,
                    payment_key,
                    _token: token,
                    _method: 'PUT'
                }).done(function (data) {
                    document.location.reload();
                }).fail(function (data) {
                    if (data.statusText === "Unauthorized") {
                        window.location.href = baseUrl + '/admin/login'
                    } else {
                        console.log(data)
                    }
                });
            })
            $('input.pm-priority').change(function (e) {
                const id = $(this).attr('id')
                const val = $(this).val()
                console.log(val)
                $.post(baseUrl + '/admin/payment-method-priority/' + id, {
                    val,
                    _token: token,
                    _method: 'PUT'
                }).done(function (data) {
                    document.location.reload();
                }).fail(function (data) {
                    if (data.statusText === "Unauthorized") {
                        window.location.href = baseUrl + '/admin/login'
                    } else {
                        console.log(data)
                    }
                });
            })
        });
    </script>
@stop
