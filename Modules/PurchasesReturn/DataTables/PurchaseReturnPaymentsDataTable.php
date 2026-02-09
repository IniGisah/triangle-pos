<?php

namespace Modules\PurchasesReturn\DataTables;

use Modules\PurchasesReturn\Entities\PurchaseReturnPayment;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class PurchaseReturnPaymentsDataTable extends DataTable
{

    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->addColumn('amount', function ($data) {
                return format_currency($data->amount);
            })
            ->addColumn('action', function ($data) {
                return view('purchasesreturn::payments.partials.actions', compact('data'));
            });
    }

    public function query(PurchaseReturnPayment $model) {
        return $model->newQuery()->byPurchaseReturn()->with('purchaseReturn');
    }

    public function html() {
        return $this->builder()
            ->setTableId('purchase-payments-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>> .
                                'tr' .
                                <'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
            ->orderBy(5)
            ->buttons(
                Button::make('excel')
                    ->text(__('purchasesreturn::purchasesreturn.excel')),
                Button::make('print')
                    ->text(__('purchasesreturn::purchasesreturn.print')),
                Button::make('reset')
                    ->text(__('purchasesreturn::purchasesreturn.reset')),
                Button::make('reload')
                    ->text(__('purchasesreturn::purchasesreturn.reload'))
            );
    }

    protected function getColumns() {
        return [
            Column::make('date')
                ->title(__('purchasesreturn::purchasesreturn.date'))
                ->className('align-middle text-center'),

            Column::make('reference')
                ->title(__('purchasesreturn::purchasesreturn.reference'))
                ->className('align-middle text-center'),

            Column::computed('amount')
                ->title(__('purchasesreturn::purchasesreturn.amount'))
                ->className('align-middle text-center'),

            Column::make('payment_method')
                ->title(__('purchasesreturn::purchasesreturn.payment_method'))
                ->className('align-middle text-center'),

            Column::computed('action')
                ->title(__('purchasesreturn::purchasesreturn.action'))
                ->exportable(false)
                ->printable(false)
                ->className('align-middle text-center'),

            Column::make('created_at')
                ->visible(false),
        ];
    }

    protected function filename(): string {
        return 'PurchaseReturnPayments_' . date('YmdHis');
    }
}
