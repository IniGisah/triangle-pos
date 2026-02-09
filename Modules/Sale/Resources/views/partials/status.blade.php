@if ($data->status == 'Pending')
    <span class="badge badge-info">
        {{ __('sale::sale.status_badge_pending') }}
    </span>
@elseif ($data->status == 'Shipped')
    <span class="badge badge-primary">
        {{ __('sale::sale.status_badge_shipped') }}
    </span>
@else
    <span class="badge badge-success">
        {{ __('sale::sale.status_badge_completed') }}
    </span>
@endif
