<?php

namespace Modules\Currency\DataTables;

use Modules\Currency\Entities\Currency;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class CurrencyDataTable extends DataTable
{

    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($data) {
                return view('currency::partials.actions', compact('data'));
            });
    }

    public function query(Currency $model) {
        return $model->newQuery();
    }

    public function html() {
        return $this->builder()
            ->setTableId('currency-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>> .
                                        'tr' .
                                        <'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
            ->orderBy(6)
            ->buttons(
                Button::make('excel')
                    ->text(__('currency::currencies.excel')),
                Button::make('print')
                    ->text(__('currency::currencies.print')),
                Button::make('reset')
                    ->text(__('currency::currencies.reset')),
                Button::make('reload')
                    ->text(__('currency::currencies.reload'))
            );
    }

    protected function getColumns() {
        return [
            Column::make('currency_name')
                ->title(__('currency::currencies.currency_name'))
                ->className('text-center align-middle'),

            Column::make('code')
                ->title(__('currency::currencies.currency_code'))
                ->className('text-center align-middle'),

            Column::make('symbol')
                ->title(__('currency::currencies.symbol'))
                ->className('text-center align-middle'),

            Column::make('thousand_separator')
                ->title(__('currency::currencies.thousand_separator'))
                ->className('text-center align-middle'),

            Column::make('decimal_separator')
                ->title(__('currency::currencies.decimal_separator'))
                ->className('text-center align-middle'),

            Column::computed('action')
                ->title(__('currency::currencies.action'))
                ->exportable(false)
                ->printable(false)
                ->className('text-center align-middle'),

            Column::make('created_at')
                ->visible(false)
        ];
    }

    protected function filename(): string {
        return 'Currency_' . date('YmdHis');
    }
}
