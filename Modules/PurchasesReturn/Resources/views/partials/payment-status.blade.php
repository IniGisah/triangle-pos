@if ($data->payment_status == 'Partial')
    <span class="badge badge-warning">
        {{ __('purchasesreturn::purchasesreturn.payment_status_partial') }}
    </span>
@elseif ($data->payment_status == 'Paid')
    <span class="badge badge-success">
        {{ __('purchasesreturn::purchasesreturn.payment_status_paid') }}
    </span>
@else
    <span class="badge badge-danger">
        {{ __('purchasesreturn::purchasesreturn.payment_status_unpaid') }}
    </span>
@endif
