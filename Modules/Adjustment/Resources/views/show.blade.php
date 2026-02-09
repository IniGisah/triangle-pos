@extends('layouts.app')

@section('title', __('adjustment::adjustments.show_title'))

@push('page_css')
    @livewireStyles
@endpush

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('nav.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('adjustments.index') }}">{{ __('adjustment::adjustments.breadcrumb_index') }}</a></li>
        <li class="breadcrumb-item active">{{ __('adjustment::adjustments.breadcrumb_details') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th colspan="2">
                                        {{ __('adjustment::adjustments.date') }}
                                    </th>
                                    <th colspan="2">
                                        {{ __('adjustment::adjustments.reference') }}
                                    </th>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        {{ $adjustment->date }}
                                    </td>
                                    <td colspan="2">
                                        {{ $adjustment->reference }}
                                    </td>
                                </tr>

                                <tr>
                                    <th>{{ __('adjustment::adjustments.product_name') }}</th>
                                    <th>{{ __('adjustment::adjustments.product_code') }}</th>
                                    <th>{{ __('adjustment::adjustments.quantity') }}</th>
                                    <th>{{ __('adjustment::adjustments.type') }}</th>
                                </tr>

                                @foreach($adjustment->adjustedProducts as $adjustedProduct)
                                    <tr>
                                        <td>{{ $adjustedProduct->product->product_name }}</td>
                                        <td>{{ $adjustedProduct->product->product_code }}</td>
                                        <td>{{ $adjustedProduct->quantity }}</td>
                                        <td>
                                            @if($adjustedProduct->type == 'add')
                                                {{ __('adjustment::adjustments.addition') }}
                                            @else
                                                {{ __('adjustment::adjustments.subtraction') }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
