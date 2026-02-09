@if ($data->status == 'Pending')
    <span class="badge badge-info">
        {{ __('quotation::quotation.status_badge_pending') }}
    </span>
@else
    <span class="badge badge-success">
        {{ __('quotation::quotation.status_badge_sent') }}
    </span>
@endif
