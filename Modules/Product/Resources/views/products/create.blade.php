@extends('layouts.app')

@section('title', __('product::product.products_create_title'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('product::product.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('product::product.products_breadcrumb') }}</a></li>
        <li class="breadcrumb-item active">{{ __('product::product.products_create_title') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid mb-4">
        <form id="product-form" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    @include('utils.alerts')
                </div>

                <div class="col-lg-12">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge badge-primary rounded-pill mr-2">1</span>
                                    <h6 class="text-uppercase text-muted mb-0">{{ __('product::product.products_details') }}</h6>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label for="product_name">{{ __('product::product.name') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="product_name" required value="{{ old('product_name') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="product_code">{{ __('product::product.code') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="product_code" required value="{{ old('product_code') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label for="category_id">{{ __('product::product.category') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select class="form-control" name="category_id" id="category_id" required>
                                                <option value="" selected disabled>{{ __('product::product.select_category') }}</option>
                                                @foreach(\Modules\Product\Entities\Category::all() as $category)
                                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append d-flex">
                                                <button data-toggle="modal" data-target="#categoryCreateModal" class="btn btn-outline-primary" type="button">
                                                    {{ __('product::product.add') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="barcode_symbology">{{ __('product::product.barcode_symbology') }} <span class="text-danger">*</span></label>
                                            <select class="form-control" name="product_barcode_symbology" id="barcode_symbology" required>
                                                <option value="" selected disabled>{{ __('product::product.select_symbology') }}</option>
                                                <option value="C128">Code 128</option>
                                                <option value="C39">Code 39</option>
                                                <option value="UPCA">UPC-A</option>
                                                <option value="UPCE">UPC-E</option>
                                                <option selected value="EAN13">EAN-13</option>
                                                <option value="EAN8">EAN-8</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4 border-top pt-4">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge badge-primary rounded-pill mr-2">2</span>
                                    <h6 class="text-uppercase text-muted mb-0">{{ __('product::product.section_pricing_tax') }}</h6>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="product_cost">{{ __('product::product.cost') }} <span class="text-danger">*</span></label>
                                            <input id="product_cost" type="text" class="form-control" name="product_cost" required value="{{ old('product_cost') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="product_price">{{ __('product::product.price') }} <span class="text-danger">*</span></label>
                                            <input id="product_price" type="text" class="form-control" name="product_price" required value="{{ old('product_price') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="retail_price">{{ __('product::product.retail_price') }}</label>
                                            <input id="retail_price" type="text" class="form-control" name="retail_price" value="{{ old('retail_price') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="product_order_tax">{{ __('product::product.tax_percentage') }}</label>
                                            <input type="number" class="form-control" name="product_order_tax" value="{{ old('product_order_tax') }}" min="0" max="100">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="product_tax_type">{{ __('product::product.tax_type') }}</label>
                                            <select class="form-control" name="product_tax_type" id="product_tax_type">
                                                <option value="" selected>{{ __('product::product.select_tax_type') }}</option>
                                                <option value="1">{{ __('product::product.tax_type_exclusive') }}</option>
                                                <option value="2">{{ __('product::product.tax_type_inclusive') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4 border-top pt-4">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge badge-primary rounded-pill mr-2">3</span>
                                    <h6 class="text-uppercase text-muted mb-0">{{ __('product::product.section_units_packaging') }}</h6>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="product_unit">{{ __('product::product.unit') }} <i class="bi bi-question-circle-fill text-info" data-toggle="tooltip" data-placement="top" title="This short text will be placed after Product Quantity."></i> <span class="text-danger">*</span></label>
                                            <select class="form-control" name="product_unit" id="product_unit" required>
                                                <option value="" selected>{{ __('product::product.select_unit') }}</option>
                                                @foreach(\Modules\Setting\Entities\Unit::all() as $unit)
                                                    <option value="{{ $unit->short_name }}">{{ $unit->name . ' | ' . $unit->short_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="wholesale_quantity">{{ __('product::product.wholesale_quantity') }}</label>
                                            <input type="number" class="form-control" name="wholesale_quantity" id="wholesale_quantity" value="{{ old('wholesale_quantity') }}" min="0" placeholder="{{ __('product::product.wholesale_quantity_placeholder') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="retail_unit">{{ __('product::product.retail_unit') }} <i class="bi bi-question-circle-fill text-info" data-toggle="tooltip" data-placement="top" title="Unit for retail stock."></i></label>
                                            <select class="form-control" name="retail_unit" id="retail_unit">
                                                <option value="" selected>{{ __('product::product.select_unit') }}</option>
                                                @foreach(\Modules\Setting\Entities\Unit::all() as $unit)
                                                    <option value="{{ $unit->short_name }}">{{ $unit->name . ' | ' . $unit->short_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4 border-top pt-4">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge badge-primary rounded-pill mr-2">4</span>
                                    <h6 class="text-uppercase text-muted mb-0">{{ __('product::product.section_inventory_alerts') }}</h6>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="product_quantity">{{ __('product::product.quantity') }} <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="product_quantity" id="product_quantity" required value="{{ old('product_quantity') }}" min="1">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="wholesale_unit_stock">{{ __('product::product.wholesale_stock') }} <i class="bi bi-question-circle-fill text-info" data-toggle="tooltip" data-placement="top" title="Number of wholesale units in stock (e.g., boxes, cartons)"></i></label>
                                            <input type="number" class="form-control" name="wholesale_unit_stock" id="wholesale_unit_stock" value="{{ old('wholesale_unit_stock', 0) }}" min="0" placeholder="Number of boxes/cartons">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="retail_unit_stock">{{ __('product::product.retail_stock') }} <i class="bi bi-question-circle-fill text-info" data-toggle="tooltip" data-placement="top" title="Number of loose retail units in stock (e.g., pieces)"></i></label>
                                            <input type="number" class="form-control" name="retail_unit_stock" id="retail_unit_stock" value="{{ old('retail_unit_stock', 0) }}" min="0" placeholder="Number of loose pieces">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="product_stock_alert">{{ __('product::product.alert_quantity') }} <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="product_stock_alert" id="product_stock_alert" required value="{{ old('product_stock_alert', 0) }}" min="0" max="100">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <div class="alert alert-info" id="stock_total_display" style="display: none;">
                                            <strong>Total Stock:</strong> <span id="stock_total_text">0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="border-top pt-4">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge badge-primary rounded-pill mr-2">5</span>
                                    <h6 class="text-uppercase text-muted mb-0">{{ __('product::product.note') }}</h6>
                                </div>
                                <div class="form-group mb-0">
                                    <textarea name="product_note" id="product_note" rows="4 " class="form-control">{{ old('product_note') }}</textarea>
                                </div>
                            </div>

                            <div class="border-top pt-4">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge badge-primary rounded-pill mr-2">6</span>
                                    <h6 class="text-uppercase text-muted mb-0">{{ __('product::product.product_images') }}</h6>
                                </div>
                                <div class="form-group mb-0">
                                    <label for="image">{{ __('product::product.product_images') }} <i class="bi bi-question-circle-fill text-info" data-toggle="tooltip" data-placement="top" title="Max Files: 3, Max File Size: 1MB, Image Size: 400x400"></i></label>
                                    <div class="dropzone d-flex flex-wrap align-items-center justify-content-center p-4 border rounded" id="document-dropzone">
                                        <div class="dz-message" data-dz-message>
                                            <i class="bi bi-cloud-arrow-up"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4 mt-4 text-right">
                                {{-- <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="text-uppercase text-muted mb-1">{{ __('product::product.products_create_title') }}</h6>
                                        <small class="text-muted">{{ __('product::product.products_details') }}</small>
                                    </div>
                                    <i class="bi bi-box-seam text-primary"></i>
                                </div> --}}
                                {{-- <p class="text-muted small mb-4">Review details, prices, stock, then save. Images are optional.</p> --}}
                                <button class="btn btn-primary btn-block">{{ __('product::product.products_create_button') }} <i class="bi bi-check"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4" style="position: sticky; top: 1rem;">
                        
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Create Category Modal -->
    @include('product::includes.category-modal')
@endsection

@section('third_party_scripts')
    <script src="{{ asset('js/dropzone.js') }}"></script>
@endsection

@push('page_scripts')
    <script>
        var uploadedDocumentMap = {}
        Dropzone.options.documentDropzone = {
            url: '{{ route('dropzone.upload') }}',
            maxFilesize: 1,
            acceptedFiles: '.jpg, .jpeg, .png',
            maxFiles: 3,
            addRemoveLinks: true,
            dictRemoveFile: "{{ __('product::product.dropzone_remove') }}",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            success: function (file, response) {
                $('form').append('<input type="hidden" name="document[]" value="' + response.name + '">');
                uploadedDocumentMap[file.name] = response.name;
            },
            removedfile: function (file) {
                file.previewElement.remove();
                var name = '';
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name;
                } else {
                    name = uploadedDocumentMap[file.name];
                }
                $.ajax({
                    type: "POST",
                    url: "{{ route('dropzone.delete') }}",
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'file_name': `${name}`
                    },
                });
                $('form').find('input[name="document[]"][value="' + name + '"]').remove();
            },
            init: function () {
                @if(isset($product) && $product->getMedia('images'))
                var files = {!! json_encode($product->getMedia('images')) !!};
                for (var i in files) {
                    var file = files[i];
                    this.options.addedfile.call(this, file);
                    this.options.thumbnail.call(this, file, file.original_url);
                    file.previewElement.classList.add('dz-complete');
                    $('form').append('<input type="hidden" name="document[]" value="' + file.file_name + '">');
                }
                @endif
            }
        }
    </script>

    <script src="{{ asset('js/jquery-mask-money.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#product_cost').maskMoney({
                prefix:'{{ settings()->currency->symbol }}',
                thousands:'{{ settings()->currency->thousand_separator }}',
                decimal:'{{ settings()->currency->decimal_separator }}',
            });
            $('#product_price').maskMoney({
                prefix:'{{ settings()->currency->symbol }}',
                thousands:'{{ settings()->currency->thousand_separator }}',
                decimal:'{{ settings()->currency->decimal_separator }}',
            });
            $('#retail_price').maskMoney({
                prefix:'{{ settings()->currency->symbol }}',
                thousands:'{{ settings()->currency->thousand_separator }}',
                decimal:'{{ settings()->currency->decimal_separator }}',
                allowZero: true,
            });

            $('#product_cost').maskMoney('mask');
            $('#product_price').maskMoney('mask');
            $('#retail_price').maskMoney('mask');

            // Calculate and display total stock across wholesale and retail breakdowns
            function updateStockTotal() {
                var wholesaleStock = parseInt($('#wholesale_unit_stock').val()) || 0;
                var retailStock = parseInt($('#retail_unit_stock').val()) || 0;
                var wholesaleQty = parseInt($('#wholesale_quantity').val()) || 0;
                var manualQty = parseInt($('#product_quantity').val()) || 0;
                var wholesaleUnit = $('#product_unit option:selected').text().split('|')[0].trim() || 'boxes';
                var retailUnit = $('#retail_unit option:selected').text().split('|')[0].trim() || $('#product_unit option:selected').text().split('|')[0].trim() || 'pcs';

                if (wholesaleQty === 0) {
                    $('#wholesale_quantity').val(0);
                    $('#wholesale_unit_stock').prop('readonly', true).addClass('bg-light');
                    $('#retail_unit_stock').prop('readonly', true).addClass('bg-light');
                    $('#retail_unit').prop('readonly', true).addClass('bg-light');
                    $('#retail_price').prop('readonly', true).addClass('bg-light');
                    $('#product_quantity').prop('readonly', false).removeClass('bg-light');
                } else {
                    $('#wholesale_unit_stock').prop('readonly', false).removeClass('bg-light');
                    $('#retail_unit_stock').prop('readonly', false).removeClass('bg-light');
                    $('#retail_unit').prop('readonly', false).removeClass('bg-light');
                    $('#retail_price').prop('readonly', false).removeClass('bg-light');
                    $('#product_quantity').prop('readonly', true).addClass('bg-light');
                }

                var totalFromBreakdown = (wholesaleStock * wholesaleQty) + retailStock;
                var total = totalFromBreakdown || manualQty;

                var displayText = total + ' ' + wholesaleUnit;
                if (totalFromBreakdown > 0 && wholesaleStock > 0 && wholesaleQty > 0) {
                    displayText = wholesaleStock + ' ' + wholesaleUnit + ' @ ' + wholesaleQty + ' + ' + retailStock + ' ' + retailUnit + ' = ' + total + ' ' + retailUnit;
                }

                

                $('#stock_total_text').text(displayText);
                $('#stock_total_display').toggle(total > 0);

                if (totalFromBreakdown > 0) {
                    $('#product_quantity').val(total);
                }
            }

            $('#product_quantity, #retail_unit_stock, #wholesale_unit_stock, #wholesale_quantity, #wholesale_unit, #retail_unit, #product_unit').on('change keyup', function() {
                updateStockTotal();
            });

            updateStockTotal();

            $('#product-form').submit(function () {
                var product_cost = $('#product_cost').maskMoney('unmasked')[0];
                var product_price = $('#product_price').maskMoney('unmasked')[0];
                var retail_price = $('#retail_price').maskMoney('unmasked')[0] || 0;

                $('#product_cost').val(product_cost);
                $('#product_price').val(product_price);
                $('#retail_price').val(retail_price);

                updateStockTotal();
            });
        });
    </script>
@endpush

