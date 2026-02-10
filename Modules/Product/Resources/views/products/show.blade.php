@extends('layouts.app')

@section('title', __('product::product.products_show_title'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('product::product.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('product::product.products_breadcrumb') }}</a></li>
        <li class="breadcrumb-item active">{{ __('product::product.products_details') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid mb-4">
        @php
            $totalQuantity = $product->total_quantity ?? $product->product_quantity;
            $wholesaleStock = $product->wholesale_unit_stock ?? 0;
            $wholesaleQuantity = $product->wholesale_quantity ?? 0;
            $retailStock = $product->retail_unit_stock ?? 0;
            $retailUnit = $product->retail_unit ?? $product->product_unit;
            $stockWorthCost = format_currency($product->product_cost * $totalQuantity);
            $stockWorthPrice = format_currency($product->product_price * $totalQuantity);
        @endphp

        <!-- Top action bar (sticky) -->
        <div class="card mb-4" style="position: sticky; top: 1rem; z-index: 1020;">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex align-items-center">
                    <h3 class="mb-0 mr-3">{{ $product->product_name }}</h3>
                    <div class="text-muted small">{{ $product->product_code }} · {{ $product->category->category_name }}</div>
                </div>

                <div class="d-flex align-items-center mt-3 mt-md-0">
                    <span class="badge badge-light mr-3">{{ __('product::product.quantity') }}: {{ $totalQuantity }} {{ $retailUnit ?? $product->product_unit }}</span>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary mr-2">{{ __('product::product.back') ?? 'Back' }}</a>
                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary mr-2">{{ __('product::product.edit') ?? 'Edit' }}</a>
                    <button class="btn btn-danger" data-toggle="modal" data-target="#deleteProductModal">{{ __('product::product.delete') ?? 'Delete' }}</button>
                </div>
            </div>
        </div>

        <style>
            /* Limit main product image height for better layout */
            .product-main-image { width: 100%; height: 360px; object-fit: contain; }
            @media (max-width: 576px) { .product-main-image { height: 220px; } }
        </style>

        <!-- Main single-column stacked content -->
        <div class="card mb-4">
            <div class="card-body">
                @php $images = $product->getMedia('images'); @endphp
                @if($images->count() > 1)
                    <div id="productCarousel" class="carousel slide mb-3" data-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($images as $idx => $media)
                                <div class="carousel-item {{ $idx == 0 ? 'active' : '' }}">
                                    <img src="{{ $media->getUrl() }}" class="d-block w-100 product-main-image" alt="Image {{ $idx + 1 }}">
                                </div>
                            @endforeach
                        </div>
                        <a class="carousel-control-prev" href="#productCarousel" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#productCarousel" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                @elseif($images->count() == 1)
                    <img src="{{ $images->first()->getUrl() }}" class="img-fluid mb-3 product-main-image" alt="Product Image">
                @else
                            <img src="{{ $product->getFirstMediaUrl('images') }}" class="img-fluid mb-3 product-main-image" alt="Product Image">

                <div class="mb-3 d-flex align-items-center">
                    <div class="mr-3">{!! \Milon\Barcode\Facades\DNS1DFacade::getBarCodeSVG($product->product_code, $product->product_barcode_symbology, 1.8, 80) !!}</div>
                    <div class="text-muted small">{{ $product->product_barcode_symbology }}</div>
                </div>

                <h5 class="text-uppercase text-muted">{{ __('product::product.products_details') }}</h5>
                <div class="row mb-3">
                    <div class="col-12 col-md-6">
                        <p class="mb-1"><strong>{{ __('product::product.code') }}:</strong> {{ $product->product_code }}</p>
                        <p class="mb-1"><strong>{{ __('product::product.category') }}:</strong> {{ $product->category->category_name }}</p>
                        <p class="mb-1"><strong>{{ __('product::product.unit') }}:</strong> {{ $product->product_unit ?? 'N/A' }}</p>
                    </div>
                    <div class="col-12 col-md-6">
                        <p class="mb-1"><strong>{{ __('product::product.wholesale_quantity') }}:</strong> @if($wholesaleQuantity > 0) {{ $wholesaleQuantity }} {{ $retailUnit }} per {{ $product->product_unit }} @else N/A @endif</p>
                        <p class="mb-1"><strong>{{ __('product::product.retail_stock') }}:</strong> {{ $retailStock }}</p>
                        <p class="mb-1"><strong>{{ __('product::product.alert_quantity') }}:</strong> {{ $product->product_stock_alert }}</p>
                    </div>
                </div>

                <h5 class="text-uppercase text-muted">{{ __('product::product.section_pricing_tax') }}</h5>
                <div class="row mb-3">
                    <div class="col-12 col-md-6 mb-2">
                        <div class="p-3 border rounded">
                            <div class="text-muted small">{{ __('product::product.cost') }}</div>
                            <div class="h5 mb-0">{{ format_currency($product->product_cost) }}</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 mb-2">
                        <div class="p-3 border rounded">
                            <div class="text-muted small">{{ __('product::product.price') }}</div>
                            <div class="h5 text-success mb-0">{{ format_currency($product->product_price) }}</div>
                        </div>
                    </div>
                </div>

                <h5 class="text-uppercase text-muted">{{ __('product::product.note') }}</h5>
                <p class="mb-0">{{ $product->product_note ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Stock History large card -->
        @if(!empty($stockHistory) && is_array($stockHistory))
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="text-uppercase text-muted mb-3">{{ __('product::product.stock_history') ?? 'Stock History' }}</h5>
                    <div style="height: 320px;">
                        <canvas id="stockChart" height="320" style="width:100%; height:100%;"></canvas>
                    </div>
                </div>
            </div>
        @endif

        <!-- Thumbnails strip -->
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="text-uppercase text-muted mb-3">{{ __('product::product.product_images') }}</h6>
                <div class="d-flex flex-wrap">
                    @forelse($product->getMedia('images') as $media)
                        <img src="{{ $media->getUrl() }}" alt="Product Image" class="img-thumbnail mr-2 mb-2" style="width: 90px; height: 90px; object-fit: cover;">
                    @empty
                        <img src="{{ $product->getFirstMediaUrl('images') }}" alt="Product Image" class="img-thumbnail" style="width: 90px; height: 90px; object-fit: cover;">
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteProductModal" tabindex="-1" role="dialog" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteProductModalLabel">{{ __('product::product.delete_confirm') ?? 'Delete product' }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{ __('product::product.delete_confirm_text') ?? 'Are you sure you want to delete this product? This action cannot be undone.' }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('product::product.cancel') ?? 'Cancel' }}</button>
                        <form id="delete-product-form" action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">{{ __('product::product.delete') ?? 'Delete' }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($stockHistory) && is_array($stockHistory))
            @push('scripts')
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var stockData = @json($stockHistory);
                        if(!stockData || !stockData.length) return;
                        var labels = stockData.map(function(s){ return s.date; });
                        var data = stockData.map(function(s){ return s.qty; });
                        var ctx = document.getElementById('stockChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: '{{ __('product::product.stock_history') ?? 'Stock' }}',
                                    data: data,
                                    borderColor: '#0d6efd',
                                    backgroundColor: 'rgba(13,110,253,0.08)',
                                    fill: true,
                                    tension: 0.3
                                }]
                            },
                            options: {
                                scales: { y: { beginAtZero: true } },
                                responsive: true,
                                maintainAspectRatio: false
                            }
                        });
                    });
                </script>
            @endpush
        @endif

    </div>
@endsection



