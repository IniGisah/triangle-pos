<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;
use Modules\Product\Entities\Product;

class SearchProduct extends Component
{

    public $query;
    public $search_results;
    public $how_many;

    public function mount() {
        $this->query = '';
        $this->how_many = 5;
        $this->search_results = Collection::empty();
    }

    public function render() {
        return view('livewire.search-product');
    }

    public function updatedQuery() {
        $this->search_results = Product::where('product_name', 'like', '%' . $this->query . '%')
            ->orWhere('product_code', 'like', '%' . $this->query . '%')
            ->take($this->how_many)->get();
    }

    public function loadMore() {
        $this->how_many += 5;
        $this->updatedQuery();
    }

    public function resetQuery() {
        $this->query = '';
        $this->how_many = 5;
        $this->search_results = Collection::empty();
    }

    public function selectProduct($product) {
        $this->dispatch('productSelected', $product);
    }

    public function searchByBarcode($rawQuery = null) {
        $query = trim((string) ($rawQuery ?? $this->query));

        if ($query === '') {
            return;
        }

        $product = Product::where('product_code', $query)->first();

        if (!$product && $this->search_results instanceof Collection && $this->search_results->count() === 1) {
            $product = $this->search_results->first();
        }

        if ($product) {
            $this->dispatch('productSelected', $product);
            $this->resetQuery();
        }
    }
}
