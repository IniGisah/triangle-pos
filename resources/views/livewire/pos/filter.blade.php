<div>
    <div class="form-row">
        <div class="col-md-7">
            <div class="form-group">
                <label>{{ __('livewire.filter.category') }}</label>
                <select wire:model.live="category" class="form-control">
                    <option value="">{{ __('livewire.filter.all_products') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label>{{ __('livewire.filter.count') }}</label>
                <select wire:model.live="showCount" class="form-control">
                    <option value="9">{{ __('livewire.filter.products_count', ['count' => 9]) }}</option>
                    <option value="15">{{ __('livewire.filter.products_count', ['count' => 15]) }}</option>
                    <option value="21">{{ __('livewire.filter.products_count', ['count' => 21]) }}</option>
                    <option value="30">{{ __('livewire.filter.products_count', ['count' => 30]) }}</option>
                    <option value="">{{ __('livewire.filter.all_products') }}</option>
                </select>
            </div>
        </div>
    </div>
</div>
