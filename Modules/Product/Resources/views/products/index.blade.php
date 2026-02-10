@extends('layouts.app')

@section('title', __('product::product.products_title'))

@section('third_party_stylesheets')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('css/product-table.css') }}">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('product::product.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('product::product.products_breadcrumb') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">{{ __('product::product.products_title') }}</h5>
                    <small class="text-muted">{{ __('product::product.products_breadcrumb') }}</small>
                </div>
                <div class="btn-group">
                    <a href="{{ route('products.create') }}" class="btn btn-primary"><i class="bi bi-plus"></i> {{ __('product::product.products_add') }}</a>
                    <button type="button" class="btn btn-outline-secondary" id="btn-export"><i class="bi bi-download"></i> Export</button>
                    <button type="button" class="btn btn-outline-secondary" id="btn-import"><i class="bi bi-upload"></i> Import</button>
                </div>
            </div>

            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <div class="input-group w-50">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search products..." data-table-search>
                    </div>

                    <div>
                        <select class="form-select" id="filter-category">
                            <option value="">All categories</option>
                            @foreach(\Modules\Product\Entities\Category::all() as $category)
                                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    {!! $dataTable->table(['class' => 'table table-striped table-hover table-bordered w-100']) !!}
                </div>
            </div>
        </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    {!! $dataTable->scripts() !!}

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.querySelector('[data-table-search]');
            const exportBtn = document.getElementById('btn-export');
            const importBtn = document.getElementById('btn-import');

            function getTable() {
                const table = document.querySelector('table.dataTable');
                return (table && $.fn.dataTable.isDataTable(table)) ? $(table).DataTable() : null;
            }

            if (searchInput) {
                let timeout = null;
                searchInput.addEventListener('input', function () {
                    clearTimeout(timeout);
                    const val = this.value;
                    timeout = setTimeout(function () {
                        const dt = getTable();
                        if (dt) dt.search(val).draw();
                    }, 250);
                });
            }

            if (exportBtn) {
                exportBtn.addEventListener('click', function () {
                    const dt = getTable();
                    if (dt && dt.button) {
                        try { dt.button('.buttons-csv').trigger(); } catch (e) { alert('Export not configured'); }
                    } else {
                        alert('Export not available');
                    }
                });
            }

            if (importBtn) {
                importBtn.addEventListener('click', function () {
                    alert('Import functionality not implemented.');
                });
            }

            // Move DataTables buttons into the card header's button group for a cleaner layout
            (function () {
                const moveInterval = setInterval(function () {
                    const dtButtons = document.querySelector('.dt-buttons');
                    const headerGroup = document.querySelector('.card-header .btn-group');
                    if (dtButtons && headerGroup) {
                        headerGroup.prepend(dtButtons);
                        clearInterval(moveInterval);
                    }
                }, 200);
            })();

            // 🔗 Connect #filter-category with DataTable (supports server-side and client-side)
            (function () {
                const filterCategory = document.getElementById('filter-category');
                if (!filterCategory) return;

                const poll = setInterval(function () {
                    const dt = getTable();
                    const table = document.querySelector('table.dataTable');
                    if (!dt || !table) return;
                    clearInterval(poll);

                    // Try to detect which column is the 'category' column by header text
                    let categoryIndex = -1;
                    table.querySelectorAll('thead th').forEach((th, idx) => {
                        if ((th.textContent || '').toLowerCase().includes('category')) categoryIndex = idx;
                    });

                    const settings = dt.settings && dt.settings()[0];
                    const isServerSide = settings && settings.oInit && settings.oInit.serverSide;

                    // If client-side and category column found, auto-populate filter options
                    if (!isServerSide && categoryIndex > -1) {
                        try {
                            const data = dt.column(categoryIndex).data().toArray();
                            const unique = Array.from(new Set(data.filter(Boolean))).sort();
                            const current = filterCategory.value;
                            filterCategory.innerHTML = '<option value="">All categories</option>';
                            unique.forEach(v => {
                                const opt = document.createElement('option');
                                opt.value = v;
                                opt.textContent = v;
                                filterCategory.appendChild(opt);
                            });
                            if (current) filterCategory.value = current;
                        } catch (e) {
                            // ignore if unable to read column data
                        }
                    }

                    // Attach preXhr for server-side so category is always sent
                    if (isServerSide && !settings._category_filter_attached) {
                        dt.on('preXhr.dt', function (e, s, data) {
                            data.category = document.getElementById('filter-category').value;
                        });
                        settings._category_filter_attached = true;
                    }

                    // Attach change handler
                    filterCategory.addEventListener('change', function () {
                        const val = this.value;
                        if (isServerSide) {
                            dt.ajax.reload();
                        } else {
                            if (categoryIndex > -1) {
                                dt.column(categoryIndex).search(val).draw();
                            } else {
                                // fallback to global search when column can't be detected
                                dt.search(val).draw();
                            }
                        }
                    });

                }, 200);
            })();
        });
    </script>
@endpush


