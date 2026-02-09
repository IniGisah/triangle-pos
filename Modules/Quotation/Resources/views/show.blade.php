@extends('layouts.app')

@section('title', __('quotation::quotation.quotations_details_title'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('quotation::quotation.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}">{{ __('quotation::quotation.quotations_breadcrumb') }}</a></li>
        <li class="breadcrumb-item active">{{ __('quotation::quotation.details') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap align-items-center">
                        <div>
                            {{ __('quotation::quotation.reference') }}: <strong>{{ $quotation->reference }}</strong>
                        </div>
                        <a target="_blank" class="btn btn-sm btn-secondary mfs-auto mfe-1 d-print-none" href="{{ route('quotations.pdf', $quotation->id) }}">
                            {!! __('quotation::quotation.print') !!}
                        </a>
                        <a target="_blank" class="btn btn-sm btn-info mfe-1 d-print-none" href="{{ route('quotations.pdf', $quotation->id) }}">
                            {{ __('quotation::quotation.save') }}
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('quotation::quotation.company_info') }}</h5>
                                <div><strong>{{ settings()->company_name }}</strong></div>
                                <div>{{ settings()->company_address }}</div>
                                <div>Email: {{ settings()->company_email }}</div>
                                <div>Phone: {{ settings()->company_phone }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('quotation::quotation.customer_info') }}</h5>
                                <div><strong>{{ $customer->customer_name }}</strong></div>
                                <div>{{ $customer->address }}</div>
                                <div>Email: {{ $customer->customer_email }}</div>
                                <div>Phone: {{ $customer->customer_phone }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('quotation::quotation.invoice_info') }}</h5>
                                <div>{{ __('quotation::quotation.invoice') }}: <strong>{{ __('quotation::quotation.invoice_prefix') }}{{ $quotation->reference }}</strong></div>
                                <div>{{ __('quotation::quotation.date') }}: {{ \Carbon\Carbon::parse($quotation->date)->format('d M, Y') }}</div>
                                <div>
                                    {{ __('quotation::quotation.status') }}: <strong>{{ __('quotation::quotation.status_badge_' . strtolower($quotation->status)) }}</strong>
                                </div>
                                <div>
                                    {{ __('quotation::quotation.payment_status') }}: <strong>{{ __('quotation::quotation.payment_status_' . strtolower($quotation->payment_status)) }}</strong>
                                </div>
                            </div>

                        </div>

                        <div class="table-responsive-sm">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="align-middle">{{ __('quotation::quotation.product') }}</th>
                                    <th class="align-middle">{{ __('quotation::quotation.net_unit_price') }}</th>
                                    <th class="align-middle">{{ __('quotation::quotation.quantity') }}</th>
                                    <th class="align-middle">{{ __('quotation::quotation.discount_col') }}</th>
                                    <th class="align-middle">{{ __('quotation::quotation.tax_col') }}</th>
                                    <th class="align-middle">{{ __('quotation::quotation.sub_total') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($quotation->quotationDetails as $item)
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
                                        <td class="left"><strong>{{ __('quotation::quotation.discount_with_rate', ['rate' => $quotation->discount_percentage]) }}</strong></td>
                                        <td class="right">{{ format_currency($quotation->discount_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>{{ __('quotation::quotation.tax_with_rate', ['rate' => $quotation->tax_percentage]) }}</strong></td>
                                        <td class="right">{{ format_currency($quotation->tax_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>{{ __('quotation::quotation.shipping') }}</strong></td>
                                        <td class="right">{{ format_currency($quotation->shipping_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>{{ __('quotation::quotation.grand_total') }}</strong></td>
                                        <td class="right"><strong>{{ format_currency($quotation->total_amount) }}</strong></td>
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

