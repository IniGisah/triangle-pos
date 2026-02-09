@if ($data->status == 'Pending')
    <span class="badge badge-info">
        {{ __('purchase::purchase.status_badge_pending') }}
    </span>
@elseif ($data->status == 'Ordered')
    <span class="badge badge-primary">
        {{ __('purchase::purchase.status_badge_ordered') }}
    </span>
@else
    <span class="badge badge-success">
        {{ __('purchase::purchase.status_badge_completed') }}
    </span>
@endif
