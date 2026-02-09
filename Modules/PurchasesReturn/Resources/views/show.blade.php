@extends('layouts.app')

@section('title', __('purchasesreturn::purchasesreturn.returns_details_title'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('purchasesreturn::purchasesreturn.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('purchase-returns.index') }}">{{ __('purchasesreturn::purchasesreturn.returns_breadcrumb') }}</a></li>
        <li class="breadcrumb-item active">{{ __('purchasesreturn::purchasesreturn.details') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap align-items-center">
                        <div>
                            {{ __('purchasesreturn::purchasesreturn.reference') }}: <strong>{{ $purchase_return->reference }}</strong>
                        </div>
                            <a target="_blank" class="btn btn-sm btn-secondary mfs-auto mfe-1 d-print-none" href="{{ route('purchase-returns.pdf', $purchase_return->id) }}">
                                {!! __('purchasesreturn::purchasesreturn.print') !!}
                            </a>
                            <a target="_blank" class="btn btn-sm btn-info mfe-1 d-print-none" href="{{ route('purchase-returns.pdf', $purchase_return->id) }}">
                                {{ __('purchasesreturn::purchasesreturn.save') }}
                            </a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('purchasesreturn::purchasesreturn.company_info') }}</h5>
                                <div><strong>{{ settings()->company_name }}</strong></div>
                                <div>{{ settings()->company_address }}</div>
                                <div>Email: {{ settings()->company_email }}</div>
                                <div>Phone: {{ settings()->company_phone }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('purchasesreturn::purchasesreturn.supplier_info') }}</h5>
                                <div><strong>{{ $supplier->supplier_name }}</strong></div>
                                <div>{{ $supplier->address }}</div>
                                <div>Email: {{ $supplier->supplier_email }}</div>
                                <div>Phone: {{ $supplier->supplier_phone }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('purchasesreturn::purchasesreturn.invoice_info') }}</h5>
                                <div>{{ __('purchasesreturn::purchasesreturn.invoice') }}: <strong>{{ __('purchasesreturn::purchasesreturn.invoice_prefix') }}{{ $purchase_return->reference }}</strong></div>
                                <div>{{ __('purchasesreturn::purchasesreturn.date') }}: {{ \Carbon\Carbon::parse($purchase_return->date)->format('d M, Y') }}</div>
                                <div>
                                    {{ __('purchasesreturn::purchasesreturn.status') }}: <strong>{{ __('purchasesreturn::purchasesreturn.status_badge_'.strtolower($purchase_return->status)) }}</strong>
                                </div>
                                <div>
                                    {{ __('purchasesreturn::purchasesreturn.payment_status') }}: <strong>{{ __('purchasesreturn::purchasesreturn.payment_status_'.strtolower($purchase_return->payment_status)) }}</strong>
                                </div>
                            </div>

                        </div>

                        <div class="table-responsive-sm">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="align-middle">{{ __('purchasesreturn::purchasesreturn.product') }}</th>
                                    <th class="align-middle">{{ __('purchasesreturn::purchasesreturn.net_unit_price') }}</th>
                                    <th class="align-middle">{{ __('purchasesreturn::purchasesreturn.quantity') }}</th>
                                    <th class="align-middle">{{ __('purchasesreturn::purchasesreturn.discount_col') }}</th>
                                    <th class="align-middle">{{ __('purchasesreturn::purchasesreturn.tax_col') }}</th>
                                    <th class="align-middle">{{ __('purchasesreturn::purchasesreturn.sub_total') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($purchase_return->purchaseReturnDetails as $item)
                                    <tr>
                                        <td class="align-middle">
                                            {{ $item->product_name }} <br>
                                            <span class="badge badge-success">
                                                {{ $item->product_code }}
                                            </span>
                                        </td>

                                        <td class="align-middle">{{ format_currency($item->unit_price) }}</td>

                                        <td class="align-middle">
                                            {{ $item->quantity }}
                                        </td>

                                        <td class="align-middle">
                                            {{ format_currency($item->product_discount_amount) }}
                                        </td>

                                        <td class="align-middle">
                                            {{ format_currency($item->product_tax_amount) }}
                                        </td>

                                        <td class="align-middle">
                                            {{ format_currency($item->sub_total) }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-sm-5 ml-md-auto">
                                <table class="table">
                                    <tbody>
                                    <tr>
                                        <td class="left"><strong>{{ __('purchasesreturn::purchasesreturn.discount_with_rate', ['rate' => $purchase_return->discount_percentage]) }}</strong></td>
                                        <td class="right">{{ format_currency($purchase_return->discount_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>{{ __('purchasesreturn::purchasesreturn.tax_with_rate', ['rate' => $purchase_return->tax_percentage]) }}</strong></td>
                                        <td class="right">{{ format_currency($purchase_return->tax_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>{{ __('purchasesreturn::purchasesreturn.shipping') }})</strong></td>
                                        <td class="right">{{ format_currency($purchase_return->shipping_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>{{ __('purchasesreturn::purchasesreturn.grand_total') }}</strong></td>
                                        <td class="right"><strong>{{ format_currency($purchase_return->total_amount) }}</strong></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

