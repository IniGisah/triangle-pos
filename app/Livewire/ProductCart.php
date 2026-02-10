<?php

namespace App\Livewire;

use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Request;
use Livewire\Component;
use Modules\Product\Entities\Product;

class ProductCart extends Component
{

    public $listeners = ['productSelected', 'discountModalRefresh'];

    public $cart_instance;
    public $global_discount;
    public $global_tax;
    public $shipping;
    public $quantity;
    public $check_quantity;
    public $discount_type;
    public $item_discount;
    public $unit_price;
    public $sale_unit;
    public $unit_multiplier;
    public $data;

    private $product;

    public function mount($cartInstance, $data = null) {
        $this->cart_instance = $cartInstance;

        if ($data) {
            $this->data = $data;

            $this->global_discount = $data->discount_percentage;
            $this->global_tax = $data->tax_percentage;
            $this->shipping = $data->shipping_amount;

            $this->updatedGlobalTax();
            $this->updatedGlobalDiscount();

            $cart_items = Cart::instance($this->cart_instance)->content();

            foreach ($cart_items as $cart_item) {
                $this->check_quantity[$cart_item->id] = $cart_item->options->stock;
                $this->quantity[$cart_item->id] = $cart_item->qty;
                $this->unit_price[$cart_item->id] = $cart_item->price;
                $this->discount_type[$cart_item->id] = $cart_item->options->product_discount_type;
                $this->sale_unit[$cart_item->id] = $cart_item->options->sale_unit ?? 'retail';
                $this->unit_multiplier[$cart_item->id] = $cart_item->options->unit_multiplier ?? 1;
                if ($cart_item->options->product_discount_type == 'fixed') {
                    $this->item_discount[$cart_item->id] = $cart_item->options->product_discount;
                } elseif ($cart_item->options->product_discount_type == 'percentage') {
                    $this->item_discount[$cart_item->id] = round(100 * ($cart_item->options->product_discount / $cart_item->price));
                }
            }
        } else {
            $this->global_discount = 0;
            $this->global_tax = 0;
            $this->shipping = 0.00;
            $this->check_quantity = [];
            $this->quantity = [];
            $this->unit_price = [];
            $this->discount_type = [];
            $this->item_discount = [];
            $this->sale_unit = [];
            $this->unit_multiplier = [];
        }
    }

    public function render() {
        $cart_items = Cart::instance($this->cart_instance)->content();

        return view('livewire.product-cart', [
            'cart_items' => $cart_items
        ]);
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

        $this->product = $product;

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
                'retail_price'          => $product['product_price'],
            ]
        ]);

        $this->check_quantity[$product['id']] = $product['product_quantity'];
        $this->quantity[$product['id']] = 1;
        $this->discount_type[$product['id']] = 'fixed';
        $this->item_discount[$product['id']] = 0;
        $this->sale_unit[$product['id']] = 'retail';
        $this->unit_multiplier[$product['id']] = $pricing['unit_multiplier'];
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

        if  ($this->cart_instance == 'sale' || $this->cart_instance == 'purchase_return' || $this->cart_instance == 'sale_return') {
            if ($this->check_quantity[$product_id] < ($this->quantity[$product_id] * $current_multiplier)) {
                session()->flash('message', __('livewire.alerts.quantity_not_available'));
                return;
            }
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

    public function updatePrice($row_id, $product_id) {
        $product = Product::findOrFail($product_id);

        $cart_item = Cart::instance($this->cart_instance)->get($row_id);

        Cart::instance($this->cart_instance)->update($row_id, ['price' => $this->unit_price[$product['id']]]);

        $pricing = $this->calculate($product->toArray(), $this->sale_unit[$product_id] ?? 'retail', $this->unit_price[$product['id']]);

        Cart::instance($this->cart_instance)->update($row_id, [
            'options' => [
                'sub_total'             => $pricing['price'] * $cart_item->qty,
                'code'                  => $cart_item->options->code,
                'stock'                 => $cart_item->options->stock,
                'unit'                  => $cart_item->options->unit,
                'product_tax'           => $pricing['product_tax'],
                'unit_price'            => $pricing['unit_price'],
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

    public function calculate($product, $saleUnit = 'retail', $new_price = null) {
        if (is_array($product) === false) {
            $product = $product->toArray();
        }

        if ($new_price) {
            $product_price = $new_price;
        } else {
            $this->unit_price[$product['id']] = $product['product_price'];
            if ($this->cart_instance == 'purchase' || $this->cart_instance == 'purchase_return') {
                $this->unit_price[$product['id']] = $product['product_cost'];
                $saleUnit = 'retail';
            } elseif ($saleUnit === 'wholesale' && !empty($product['wholesale_price'])) {
                $this->unit_price[$product['id']] = $product['wholesale_price'];
            }
            $product_price = $this->unit_price[$product['id']];
        }
        $price = 0;
        $unit_price = 0;
        $product_tax = 0;
        $sub_total = 0;
        $unit_multiplier = 1;
        $sale_unit_label = $product['product_unit'];

        if (($saleUnit === 'wholesale') && !empty($product['wholesale_quantity'])) {
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
            'unit'                  => $cart_item->options->unit,
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

        if ($this->cart_instance == 'purchase' || $this->cart_instance == 'purchase_return') {
            return;
        }

        $pricing = $this->calculate($product, $sale_unit);
        $requested_base = $this->quantity[$product_id] * $pricing['unit_multiplier'];

        if (($this->cart_instance == 'sale' || $this->cart_instance == 'sale_return') && $this->check_quantity[$product_id] < $requested_base) {
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
