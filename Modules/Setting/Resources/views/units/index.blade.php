@extends('layouts.app')

@section('title', __('setting::setting.units_title'))

@section('third_party_stylesheets')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.3/css/select.dataTables.min.css">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('setting::setting.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('setting::setting.units_breadcrumb') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <a href="{{ route('units.create') }}" class="btn btn-primary">
                            {{ __('setting::setting.units_add') }} <i class="bi bi-plus"></i>
                        </a>

                        <hr>

                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 text-center" id="data-table">
                                <thead>
                                <tr>
                                    <th class="align-middle">{{ __('setting::setting.no') }}</th>
                                    <th class="align-middle">{{ __('setting::setting.name') }}</th>
                                    <th class="align-middle">{{ __('setting::setting.short_name') }}</th>
                                    <th class="align-middle">{{ __('setting::setting.operator') }}</th>
                                    <th class="align-middle">{{ __('setting::setting.operation_value') }}</th>
                                    <th class="align-middle">{{ __('setting::setting.action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($units as $key => $unit)
                                    <tr>
                                        <td class="align-middle">{{ $key + 1 }}</td>
                                        <td class="align-middle">{{ $unit->name }}</td>
                                        <td class="align-middle">{{ $unit->short_name }}</td>
                                        <td class="align-middle">{{ $unit->operator }}</td>
                                        <td class="align-middle">{{ $unit->operation_value }}</td>
                                        <td class="align-middle">
                                            <a href="{{ route('units.edit', $unit) }}" class="btn btn-primary btn-sm">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button id="delete" class="btn btn-danger btn-sm delete-confirm" onclick="
                                                event.preventDefault();
                                                if (confirm('{{ __('setting::setting.confirm_delete') }}')) {
                                                document.getElementById('destroy{{ $unit->id }}').submit()
                                                }
                                                ">
                                                <i class="bi bi-trash"></i>
                                                <form id="destroy{{ $unit->id }}" class="d-none" action="{{ route('units.destroy', $unit) }}"
                                                      method="POST">
                                                    @csrf
                                                    @method('delete')
                                                </form>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.24/b-1.7.0/b-html5-1.7.0/b-print-1.7.0/datatables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
    <script>
        var table = $('#data-table').DataTable({
            dom: "<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4 justify-content-end'f>>tr<'row'<'col-md-5'i><'col-md-7 mt-2'p>>",
            "buttons": [
                {extend: 'excel',text: '{!! __('setting::setting.excel') !!}'},
                {extend: 'csv',text: '{!! __('setting::setting.csv') !!}'},
                {extend: 'print',
                    text: '{!! __('setting::setting.print') !!}',
                    title: "{{ __('setting::setting.units_print_title') }}",
                    exportOptions: {
                        columns: [ 0, 1, 2, 3, 4 ]
                    },
                    customize: function (win) {
                        $(win.document.body).find('h1').css('font-size', '15pt');
                        $(win.document.body).find('h1').css('text-align', 'center');
                        $(win.document.body).find('h1').css('margin-bottom', '20px');
                        $(win.document.body).css('margin', '35px 25px');
                    }
                },
            ],
            ordering: false,
        });
    </script>
@endpush
