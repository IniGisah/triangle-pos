<?php

namespace Modules\Product\DataTables;

use Modules\Product\Entities\Category;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ProductCategoriesDataTable extends DataTable
{

    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($data) {
                return view('product::categories.partials.actions', compact('data'));
            });
    }

    public function query(Category $model) {
        return $model->newQuery()->withCount('products');
    }

    public function html() {
        return $this->builder()
            ->setTableId('product_categories-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>> .
                                'tr' .
                                <'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
            ->orderBy(4)
            ->buttons(
                Button::make('excel')
                    ->text(__('product::product.excel')),
                Button::make('print')
                    ->text(__('product::product.print')),
                Button::make('reset')
                    ->text(__('product::product.reset')),
                Button::make('reload')
                    ->text(__('product::product.reload'))
            );
    }

    protected function getColumns() {
        return [
            Column::make('category_code')
                ->title(__('product::product.code'))
                ->addClass('text-center'),

            Column::make('category_name')
                ->title(__('product::product.name'))
                ->addClass('text-center'),

            Column::make('products_count')
                ->title(__('product::product.products_count'))
                ->addClass('text-center'),

            Column::computed('action')
                ->title(__('product::product.action'))
                ->exportable(false)
                ->printable(false)
                ->addClass('text-center'),

            Column::make('created_at')
                ->visible(false)
        ];
    }

    protected function filename(): string {
        return 'ProductCategories_' . date('YmdHis');
    }
}
