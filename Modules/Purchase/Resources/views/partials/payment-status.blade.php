@if ($data->payment_status == 'Partial')
    <span class="badge badge-warning">
        {{ __('purchase::purchase.payment_status_partial') }}
    </span>
@elseif ($data->payment_status == 'Paid')
    <span class="badge badge-success">
        {{ __('purchase::purchase.payment_status_paid') }}
    </span>
@else
    <span class="badge badge-danger">
        {{ __('purchase::purchase.payment_status_unpaid') }}
    </span>
@endif
