@if ($data->status == 'Pending')
    <span class="badge badge-info">
        {{ __('purchasesreturn::purchasesreturn.status_badge_pending') }}
    </span>
@elseif ($data->status == 'Shipped')
    <span class="badge badge-primary">
        {{ __('purchasesreturn::purchasesreturn.status_badge_shipped') }}
    </span>
@else
    <span class="badge badge-success">
        {{ __('purchasesreturn::purchasesreturn.status_badge_completed') }}
    </span>
@endif
