@if ($data->payment_status == 'Partial')
    <span class="badge badge-warning">
        {{ __('sale::sale.payment_status_badge_partial') }}
    </span>
@elseif ($data->payment_status == 'Paid')
    <span class="badge badge-success">
        {{ __('sale::sale.payment_status_badge_paid') }}
    </span>
@else
    <span class="badge badge-danger">
        {{ __('sale::sale.payment_status_badge_unpaid') }}
    </span>
@endif
