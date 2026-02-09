<?php

namespace Modules\People\DataTables;


use Modules\People\Entities\Customer;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class CustomersDataTable extends DataTable
{

    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($data) {
                return view('people::customers.partials.actions', compact('data'));
            });
    }

    public function query(Customer $model) {
        return $model->newQuery();
    }

    public function html() {
        return $this->builder()
            ->setTableId('customers-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>> .
                                       'tr' .
                                 <'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
            ->orderBy(4)
            ->buttons(
                Button::make('excel')
                    ->text(__('people::people.excel')),
                Button::make('print')
                    ->text(__('people::people.print')),
                Button::make('reset')
                    ->text(__('people::people.reset')),
                Button::make('reload')
                    ->text(__('people::people.reload'))
            );
    }

    protected function getColumns() {
        return [
            Column::make('customer_name')
                ->title(__('people::people.name'))
                ->className('text-center align-middle'),

            Column::make('customer_email')
                ->title(__('people::people.email'))
                ->className('text-center align-middle'),

            Column::make('customer_phone')
                ->title(__('people::people.phone'))
                ->className('text-center align-middle'),

            Column::computed('action')
                ->title(__('people::people.action'))
                ->exportable(false)
                ->printable(false)
                ->className('text-center align-middle'),

            Column::make('created_at')
                ->visible(false)
        ];
    }

    protected function filename(): string {
        return 'Customers_' . date('YmdHis');
    }
}
