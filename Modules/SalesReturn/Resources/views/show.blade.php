@extends('layouts.app')

@section('title', __('salesreturn::salesreturn.returns_details_title'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('salesreturn::salesreturn.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sale-returns.index') }}">{{ __('salesreturn::salesreturn.returns_breadcrumb') }}</a></li>
        <li class="breadcrumb-item active">{{ __('salesreturn::salesreturn.details') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap align-items-center">
                        <div>
                            {{ __('salesreturn::salesreturn.reference') }}: <strong>{{ $sale_return->reference }}</strong>
                        </div>
                        <a target="_blank" class="btn btn-sm btn-secondary mfs-auto mfe-1 d-print-none" href="{{ route('sale-returns.pdf', $sale_return->id) }}">
                            {!! __('salesreturn::salesreturn.print') !!}
                        </a>
                        <a target="_blank" class="btn btn-sm btn-info mfe-1 d-print-none" href="{{ route('sale-returns.pdf', $sale_return->id) }}">
                            <i class="bi bi-save"></i> {{ __('salesreturn::salesreturn.save') }}
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('salesreturn::salesreturn.company_info') }}</h5>
                                <div><strong>{{ settings()->company_name }}</strong></div>
                                <div>{{ settings()->company_address }}</div>
                                <div>{{ __('salesreturn::salesreturn.email') }}: {{ settings()->company_email }}</div>
                                <div>{{ __('salesreturn::salesreturn.phone') }}: {{ settings()->company_phone }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('salesreturn::salesreturn.customer_info') }}</h5>
                                <div><strong>{{ $customer->customer_name }}</strong></div>
                                <div>{{ $customer->address }}</div>
                                <div>{{ __('salesreturn::salesreturn.email') }}: {{ $customer->customer_email }}</div>
                                <div>{{ __('salesreturn::salesreturn.phone') }}: {{ $customer->customer_phone }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('salesreturn::salesreturn.invoice_info') }}</h5>
                                <div>{{ __('salesreturn::salesreturn.invoice') }}: <strong>{{ __('salesreturn::salesreturn.invoice_prefix') }}{{ $sale_return->reference }}</strong></div>
                                <div>{{ __('salesreturn::salesreturn.date') }}: {{ \Carbon\Carbon::parse($sale_return->date)->format('d M, Y') }}</div>
                                <div>
                                    {{ __('salesreturn::salesreturn.status') }}: <strong>{{ __('salesreturn::salesreturn.status_badge_' . strtolower($sale_return->status)) }}</strong>
                                </div>
                                <div>
                                    {{ __('salesreturn::salesreturn.payment_status') }}: <strong>{{ __('salesreturn::salesreturn.payment_status_badge_' . strtolower($sale_return->payment_status)) }}</strong>
                                </div>
                            </div>

                        </div>

                        <div class="table-responsive-sm">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="align-middle">{{ __('salesreturn::salesreturn.product') }}</th>
                                    <th class="align-middle">{{ __('salesreturn::salesreturn.net_unit_price') }}</th>
                                    <th class="align-middle">{{ __('salesreturn::salesreturn.quantity') }}</th>
                                    <th class="align-middle">{{ __('salesreturn::salesreturn.discount_col') }}</th>
                                    <th class="align-middle">{{ __('salesreturn::salesreturn.tax_col') }}</th>
                                    <th class="align-middle">{{ __('salesreturn::salesreturn.sub_total') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($sale_return->saleReturnDetails as $item)
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
                                        <td class="left"><strong>{{ __('salesreturn::salesreturn.discount_with_rate', ['rate' => $sale_return->discount_percentage]) }}</strong></td>
                                        <td class="right">{{ format_currency($sale_return->discount_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>{{ __('salesreturn::salesreturn.tax_with_rate', ['rate' => $sale_return->tax_percentage]) }}</strong></td>
                                        <td class="right">{{ format_currency($sale_return->tax_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>{{ __('salesreturn::salesreturn.shipping') }}</strong></td>
                                        <td class="right">{{ format_currency($sale_return->shipping_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>{{ __('salesreturn::salesreturn.grand_total') }}</strong></td>
                                        <td class="right"><strong>{{ format_currency($sale_return->total_amount) }}</strong></td>
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

