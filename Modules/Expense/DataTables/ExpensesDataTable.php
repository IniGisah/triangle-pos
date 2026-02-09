<?php

namespace Modules\Expense\DataTables;

use Modules\Expense\Entities\Expense;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ExpensesDataTable extends DataTable
{

    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->addColumn('amount', function ($data) {
                return format_currency($data->amount);
            })
            ->addColumn('action', function ($data) {
                return view('expense::expenses.partials.actions', compact('data'));
            });
    }

    public function query(Expense $model) {
        return $model->newQuery()->with('category');
    }

    public function html() {
        return $this->builder()
            ->setTableId('expenses-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>> .
                                'tr' .
                                <'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
            ->orderBy(6)
            ->buttons(
                Button::make('excel')
                    ->text(__('expense::expenses.excel')),
                Button::make('print')
                    ->text(__('expense::expenses.print')),
                Button::make('reset')
                    ->text(__('expense::expenses.reset')),
                Button::make('reload')
                    ->text(__('expense::expenses.reload'))
            );
    }

    protected function getColumns() {
        return [
            Column::make('date')
                ->title(__('expense::expenses.date'))
                ->className('text-center align-middle'),

            Column::make('reference')
                ->title(__('expense::expenses.reference'))
                ->className('text-center align-middle'),

            Column::make('category.category_name')
                ->title(__('expense::expenses.category'))
                ->className('text-center align-middle'),

            Column::computed('amount')
                ->title(__('expense::expenses.amount'))
                ->className('text-center align-middle'),

            Column::make('details')
                ->title(__('expense::expenses.details'))
                ->className('text-center align-middle'),

            Column::computed('action')
                ->title(__('expense::expenses.action'))
                ->exportable(false)
                ->printable(false)
                ->className('text-center align-middle'),

            Column::make('created_at')
                ->visible(false)
        ];
    }

    protected function filename(): string {
        return 'Expenses_' . date('YmdHis');
    }
}
