<?php

return [
    'common' => [
        'loading' => 'Loading...',
        'close' => 'Close',
        'save_changes' => 'Save changes',
    ],

    'alerts' => [
        'no_products' => 'Please search & select products!',
        'no_product' => 'Please search & select a product!',
        'select_customer' => 'Please select a customer!',
        'product_exists' => 'Product exists in the cart!',
        'quantity_not_available' => 'The requested quantity is not available in stock.',
        'discount_added' => 'Discount added to the product!',
    ],

    'pos' => [
        'customer' => 'Customer',
        'select_customer' => 'Select Customer',
        'add_customer' => 'Add Customer',
        'product' => 'Product',
        'price' => 'Price',
        'quantity' => 'Quantity',
        'action' => 'Action',
        'order_tax_label' => 'Order Tax (:percent%)',
        'discount_label' => 'Discount (:percent%)',
        'shipping' => 'Shipping',
        'grand_total' => 'Grand Total',
        'order_tax_input' => 'Order Tax (%)',
        'discount_input' => 'Discount (%)',
        'shipping_input' => 'Shipping',
        'reset' => 'Reset',
        'proceed' => 'Proceed',
        'remove' => 'Remove',
        'please_select_products' => 'Please search & select products!',
        'total_products' => 'Total Products',
    ],

    'cart' => [
        'net_unit_price' => 'Net Unit Price',
        'stock' => 'Stock',
        'discount' => 'Discount',
        'tax' => 'Tax',
        'sub_total' => 'Sub Total',
    ],

    'product_list' => [
        'stock_badge' => 'Stock: :stock',
        'not_found' => 'Products Not Found...',
    ],

    'filter' => [
        'category' => 'Product Category',
        'all_products' => 'All Products',
        'count' => 'Product Count',
        'products_count' => ':count Products',
    ],

    'search' => [
        'placeholder' => 'Type product name or code....',
        'load_more' => 'Load More',
        'no_product_found' => 'No Product Found....',
    ],

    'checkout_modal' => [
        'title' => 'Confirm Sale',
        'total_amount' => 'Total Amount',
        'received_amount' => 'Received Amount',
        'payment_method' => 'Payment Method',
        'payment_cash' => 'Cash',
        'payment_credit_card' => 'Credit Card',
        'payment_bank_transfer' => 'Bank Transfer',
        'payment_cheque' => 'Cheque',
        'payment_other' => 'Other',
        'note' => 'Note (If Needed)',
        'grand_total' => 'Grand Total',
        'submit' => 'Submit',
    ],

    'discount_modal' => [
        'discount_type' => 'Discount Type',
        'fixed' => 'Fixed',
        'percentage' => 'Percentage',
        'discount_percent' => 'Discount(%)',
        'discount' => 'Discount',
    ],

    'barcode' => [
        'product_name' => 'Product Name',
        'code' => 'Code',
        'quantity' => 'Quantity',
        'quantity_help' => 'Max Quantity: 100',
        'generate' => 'Generate Barcodes',
        'download_pdf' => 'Download PDF',
        'price_label' => 'Price:',
        'max_quantity_error' => 'Max quantity is 100 per barcode generation!',
        'invalid_code_error' => 'Cannot generate a barcode with this type of product code.',
    ],
];
