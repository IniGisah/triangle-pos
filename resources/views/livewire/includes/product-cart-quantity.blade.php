<div class="d-flex flex-column align-items-center">
    <div class="input-group d-flex justify-content-center">
        <div class="input-group-prepend">
            <span class="input-group-text">
                {{ $cart_item->options->sale_unit_label ?? $cart_item->options->unit }}
            </span>
        </div>
        <input wire:model="quantity.{{ $cart_item->id }}" style="min-width: 40px;max-width: 90px;" type="number" class="form-control" value="{{ $cart_item->qty }}" min="1">
        <div class="input-group-append">
            <button type="button" wire:click="updateQuantity('{{ $cart_item->rowId }}', {{ $cart_item->id }})" class="btn btn-info">
                <i class="bi bi-check"></i>
            </button>
        </div>
    </div>

    @if(!empty($cart_item->options->wholesale_quantity) && !empty($cart_item->options->wholesale_price))
        <div class="mt-2 w-100">
            @php
                $packUnit = $cart_item->options->wholesale_unit ?? __('sale::sale.wholesale_unit_default');
            @endphp
            <select wire:model.live="sale_unit.{{ $cart_item->id }}" wire:change="changeSaleUnit('{{ $cart_item->rowId }}', {{ $cart_item->id }}, $event.target.value)" class="form-control form-control-sm">
                <option value="retail">{{ __('sale::sale.retail_label', ['unit' => $cart_item->options->unit]) }}</option>
                <option value="wholesale">{{ __('sale::sale.wholesale_label', ['unit' => $packUnit]) }}</option>
            </select>
            <small class="text-muted">{{ __('sale::sale.wholesale_hint', ['pack' => $packUnit, 'qty' => $cart_item->options->wholesale_quantity, 'unit' => $cart_item->options->unit]) }}</small>
        </div>
    @endif
</div>
