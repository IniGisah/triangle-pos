<?php

namespace Modules\Sale\DataTables;

use Modules\Sale\Entities\Sale;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class SalesDataTable extends DataTable
{

    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->addColumn('total_amount', function ($data) {
                return format_currency($data->total_amount);
            })
            ->addColumn('paid_amount', function ($data) {
                return format_currency($data->paid_amount);
            })
            ->addColumn('due_amount', function ($data) {
                return format_currency($data->due_amount);
            })
            ->addColumn('status', function ($data) {
                return view('sale::partials.status', compact('data'));
            })
            ->addColumn('payment_status', function ($data) {
                return view('sale::partials.payment-status', compact('data'));
            })
            ->addColumn('action', function ($data) {
                return view('sale::partials.actions', compact('data'));
            });
    }

    public function query(Sale $model) {
        return $model->newQuery();
    }

    public function html() {
        return $this->builder()
            ->setTableId('sales-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>> .
                                'tr' .
                                <'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
            ->orderBy(8)
            ->buttons(
                Button::make('excel')
                    ->text(__('sale::sale.excel')),
                Button::make('print')
                    ->text(__('sale::sale.print')),
                Button::make('reset')
                    ->text(__('sale::sale.reset')),
                Button::make('reload')
                    ->text(__('sale::sale.reload'))
            );
    }

    protected function getColumns() {
        return [
            Column::make('reference')
                ->title(__('sale::sale.reference'))
                ->className('text-center align-middle'),

            Column::make('customer_name')
                ->title(__('sale::sale.customer'))
                ->className('text-center align-middle'),

            Column::computed('status')
                ->title(__('sale::sale.status'))
                ->className('text-center align-middle'),

            Column::computed('total_amount')
                ->title(__('sale::sale.grand_total'))
                ->className('text-center align-middle'),

            Column::computed('paid_amount')
                ->title(__('sale::sale.paid_amount'))
                ->className('text-center align-middle'),

            Column::computed('due_amount')
                ->title(__('sale::sale.due_amount'))
                ->className('text-center align-middle'),

            Column::computed('payment_status')
                ->title(__('sale::sale.payment_status'))
                ->className('text-center align-middle'),

            Column::computed('action')
                ->title(__('sale::sale.action'))
                ->exportable(false)
                ->printable(false)
                ->className('text-center align-middle'),

            Column::make('created_at')
                ->visible(false)
        ];
    }

    protected function filename(): string {
        return 'Sales_' . date('YmdHis');
    }
}
