<?php

namespace App\Livewire\Pos;

use Gloudemans\Shoppingcart\Facades\Cart;
use Livewire\Component;
use Modules\Product\Entities\Product;

class Checkout extends Component
{

    public $listeners = ['productSelected', 'discountModalRefresh'];

    public $cart_instance;
    public $customers;
    public $global_discount;
    public $global_tax;
    public $shipping;
    public $quantity;
    public $check_quantity;
    public $discount_type;
    public $item_discount;
    public $sale_unit;
    public $unit_multiplier;
    public $data;
    public $customer_id;
    public $total_amount;

    public function mount($cartInstance, $customers) {
        $this->cart_instance = $cartInstance;
        $this->customers = $customers;
        $this->global_discount = 0;
        $this->global_tax = 0;
        $this->shipping = 0.00;
        $this->check_quantity = [];
        $this->quantity = [];
        $this->discount_type = [];
        $this->item_discount = [];
        $this->sale_unit = [];
        $this->unit_multiplier = [];
        $this->total_amount = 0;
    }

    public function hydrate() {
        $this->total_amount = $this->calculateTotal();
    }

    public function render() {
        $cart_items = Cart::instance($this->cart_instance)->content();

        return view('livewire.pos.checkout', [
            'cart_items' => $cart_items
        ]);
    }

    public function proceed() {
        if ($this->customer_id != null) {
            $this->dispatch('showCheckoutModal');
        } else {
            session()->flash('message', __('livewire.alerts.select_customer'));
        }
    }

    public function calculateTotal() {
        return Cart::instance($this->cart_instance)->total() + $this->shipping;
    }

    public function resetCart() {
        Cart::instance($this->cart_instance)->destroy();
    }

    public function productSelected($product) {
        $cart = Cart::instance($this->cart_instance);

        $exists = $cart->search(function ($cartItem, $rowId) use ($product) {
            return $cartItem->id == $product['id'];
        });

        if ($exists->isNotEmpty()) {
            session()->flash('message', __('livewire.alerts.product_exists'));

            return;
        }

        $pricing = $this->calculate($product);

        $cart->add([
            'id'      => $product['id'],
            'name'    => $product['product_name'],
            'qty'     => 1,
            'price'   => $pricing['price'],
            'weight'  => 1,
            'options' => [
                'product_discount'      => 0.00,
                'product_discount_type' => 'fixed',
                'sub_total'             => $pricing['sub_total'],
                'code'                  => $product['product_code'],
                'stock'                 => $product['product_quantity'],
                'unit'                  => $product['product_unit'],
                'product_tax'           => $pricing['product_tax'],
                'unit_price'            => $pricing['unit_price'],
                'sale_unit'             => 'retail',
                'sale_unit_label'       => $pricing['sale_unit_label'],
                'unit_multiplier'       => $pricing['unit_multiplier'],
                'wholesale_unit'        => $product['wholesale_unit'] ?? null,
                'wholesale_quantity'    => $product['wholesale_quantity'] ?? null,
                'wholesale_price'       => $product['wholesale_price'] ?? null,
            ]
        ]);

        $this->check_quantity[$product['id']] = $product['product_quantity'];
        $this->quantity[$product['id']] = 1;
        $this->discount_type[$product['id']] = 'fixed';
        $this->item_discount[$product['id']] = 0;
        $this->sale_unit[$product['id']] = 'retail';
        $this->unit_multiplier[$product['id']] = $pricing['unit_multiplier'];
        $this->total_amount = $this->calculateTotal();
    }

    public function removeItem($row_id) {
        Cart::instance($this->cart_instance)->remove($row_id);
    }

    public function updatedGlobalTax() {
        Cart::instance($this->cart_instance)->setGlobalTax((integer)$this->global_tax);
    }

    public function updatedGlobalDiscount() {
        Cart::instance($this->cart_instance)->setGlobalDiscount((integer)$this->global_discount);
    }

    public function updateQuantity($row_id, $product_id) {
        $current_multiplier = $this->unit_multiplier[$product_id] ?? 1;

        if ($this->check_quantity[$product_id] < ($this->quantity[$product_id] * $current_multiplier)) {
            session()->flash('message', __('livewire.alerts.quantity_not_available'));

            return;
        }

        Cart::instance($this->cart_instance)->update($row_id, $this->quantity[$product_id]);

        $cart_item = Cart::instance($this->cart_instance)->get($row_id);

        Cart::instance($this->cart_instance)->update($row_id, [
            'options' => [
                'sub_total'             => $cart_item->price * $cart_item->qty,
                'code'                  => $cart_item->options->code,
                'stock'                 => $cart_item->options->stock,
                'unit'                  => $cart_item->options->unit,
                'product_tax'           => $cart_item->options->product_tax,
                'unit_price'            => $cart_item->options->unit_price,
                'sale_unit'             => $cart_item->options->sale_unit,
                'sale_unit_label'       => $cart_item->options->sale_unit_label,
                'unit_multiplier'       => $cart_item->options->unit_multiplier,
                'wholesale_unit'        => $cart_item->options->wholesale_unit,
                'wholesale_quantity'    => $cart_item->options->wholesale_quantity,
                'wholesale_price'       => $cart_item->options->wholesale_price,
                'product_discount'      => $cart_item->options->product_discount,
                'product_discount_type' => $cart_item->options->product_discount_type,
            ]
        ]);
    }

    public function updatedDiscountType($value, $name) {
        $this->item_discount[$name] = 0;
    }

    public function discountModalRefresh($product_id, $row_id) {
        $this->updateQuantity($row_id, $product_id);
    }

    public function setProductDiscount($row_id, $product_id) {
        $cart_item = Cart::instance($this->cart_instance)->get($row_id);

        if ($this->discount_type[$product_id] == 'fixed') {
            Cart::instance($this->cart_instance)
                ->update($row_id, [
                    'price' => ($cart_item->price + $cart_item->options->product_discount) - $this->item_discount[$product_id]
                ]);

            $discount_amount = $this->item_discount[$product_id];

            $this->updateCartOptions($row_id, $product_id, $cart_item, $discount_amount);
        } elseif ($this->discount_type[$product_id] == 'percentage') {
            $discount_amount = ($cart_item->price + $cart_item->options->product_discount) * ($this->item_discount[$product_id] / 100);

            Cart::instance($this->cart_instance)
                ->update($row_id, [
                    'price' => ($cart_item->price + $cart_item->options->product_discount) - $discount_amount
                ]);

            $this->updateCartOptions($row_id, $product_id, $cart_item, $discount_amount);
        }

        session()->flash('discount_message' . $product_id, __('livewire.alerts.discount_added'));
    }

    public function calculate($product, $saleUnit = 'retail') {
        $price = 0;
        $unit_price = 0;
        $product_tax = 0;
        $sub_total = 0;
        $unit_multiplier = 1;
        $sale_unit_label = $product['product_unit'];
        $product_price = $product['product_price'];

        if ($saleUnit === 'wholesale' && !empty($product['wholesale_price']) && !empty($product['wholesale_quantity'])) {
            $product_price = $product['wholesale_price'];
            $unit_multiplier = $product['wholesale_quantity'];
            $sale_unit_label = $product['wholesale_unit'] ?? $sale_unit_label;
        }

        if ($product['product_tax_type'] == 1) {
            $price = $product_price + ($product_price * ($product['product_order_tax'] / 100));
            $unit_price = $product_price;
            $product_tax = $product_price * ($product['product_order_tax'] / 100);
            $sub_total = $product_price + ($product_price * ($product['product_order_tax'] / 100));
        } elseif ($product['product_tax_type'] == 2) {
            $price = $product_price;
            $unit_price = $product_price - ($product_price * ($product['product_order_tax'] / 100));
            $product_tax = $product_price * ($product['product_order_tax'] / 100);
            $sub_total = $product_price;
        } else {
            $price = $product_price;
            $unit_price = $product_price;
            $product_tax = 0.00;
            $sub_total = $product_price;
        }

        return [
            'price' => $price,
            'unit_price' => $unit_price,
            'product_tax' => $product_tax,
            'sub_total' => $sub_total,
            'unit_multiplier' => $unit_multiplier,
            'sale_unit_label' => $sale_unit_label,
        ];
    }

    public function updateCartOptions($row_id, $product_id, $cart_item, $discount_amount) {
        Cart::instance($this->cart_instance)->update($row_id, ['options' => [
            'sub_total'             => $cart_item->price * $cart_item->qty,
            'code'                  => $cart_item->options->code,
            'stock'                 => $cart_item->options->stock,
            'unit'                 => $cart_item->options->unit,
            'product_tax'           => $cart_item->options->product_tax,
            'unit_price'            => $cart_item->options->unit_price,
            'sale_unit'             => $cart_item->options->sale_unit,
            'sale_unit_label'       => $cart_item->options->sale_unit_label,
            'unit_multiplier'       => $cart_item->options->unit_multiplier,
            'wholesale_unit'        => $cart_item->options->wholesale_unit,
            'wholesale_quantity'    => $cart_item->options->wholesale_quantity,
            'wholesale_price'       => $cart_item->options->wholesale_price,
            'product_discount'      => $discount_amount,
            'product_discount_type' => $this->discount_type[$product_id],
        ]]);
    }

    public function changeSaleUnit($row_id, $product_id, $sale_unit) {
        $product = Product::findOrFail($product_id)->toArray();

        $pricing = $this->calculate($product, $sale_unit);
        $requested_base = $this->quantity[$product_id] * $pricing['unit_multiplier'];

        if ($this->check_quantity[$product_id] < $requested_base) {
            session()->flash('message', __('livewire.alerts.quantity_not_available'));

            return;
        }

        $this->sale_unit[$product_id] = $sale_unit;
        $this->unit_multiplier[$product_id] = $pricing['unit_multiplier'];

        Cart::instance($this->cart_instance)->update($row_id, ['price' => $pricing['price']]);

        $cart_item = Cart::instance($this->cart_instance)->get($row_id);

        Cart::instance($this->cart_instance)->update($row_id, [
            'options' => [
                'sub_total'             => $pricing['price'] * $cart_item->qty,
                'code'                  => $cart_item->options->code,
                'stock'                 => $cart_item->options->stock,
                'unit'                  => $cart_item->options->unit,
                'product_tax'           => $pricing['product_tax'],
                'unit_price'            => $pricing['unit_price'],
                'sale_unit'             => $sale_unit,
                'sale_unit_label'       => $pricing['sale_unit_label'],
                'unit_multiplier'       => $pricing['unit_multiplier'],
                'wholesale_unit'        => $product['wholesale_unit'] ?? null,
                'wholesale_quantity'    => $product['wholesale_quantity'] ?? null,
                'wholesale_price'       => $product['wholesale_price'] ?? null,
                'product_discount'      => $cart_item->options->product_discount,
                'product_discount_type' => $cart_item->options->product_discount_type,
            ]
        ]);
    }
}
