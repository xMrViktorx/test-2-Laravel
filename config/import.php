<?php
return [
    'orders' => [
        'label' => 'Import Orders',
        'permission_required' => 'import-orders',
        'files' => [
           'orders' => [
                'label' => 'Order File',
                'headers_to_db' => [
                    'order_date' => [
                        'label' => 'Order Date',
                        'type' => 'date',
                        'validation' => ['required'],
                    ],
                    'channel' => [
                        'label' => 'Channel',
                        'type' => 'string',
                        'validation' => ['required', 'in' => ['PT', 'Amazon']],
                    ],
                    'sku' => [
                        'label' => 'SKU',
                        'type' => 'string',
                        'validation' => ['required', 'exists' => ['table' => 'products', 'column' => 'sku']],
                    ],
                    'item_description' => [
                        'label' => 'Item Description',
                        'type' => 'string',
                        'validation' => ['nullable'],
                    ],
                    'origin' => [
                        'label' => 'Origin',
                        'type' => 'string',
                        'validation' => ['required'],
                    ],
                    'so_num' => [
                        'label' => 'SO#',
                        'type' => 'string',
                        'validation' => ['required'],
                    ],
                    'cost' => [
                        'label' => 'Cost',
                        'type' => 'double',
                        'validation' => ['required'],
                    ],
                    'shipping_cost' => [
                        'label' => 'Shipping Cost',
                        'type' => 'double',
                        'validation' => ['required'],
                    ],
                    'total_price' => [
                        'label' => 'Total Price',
                        'type' => 'double',
                        'validation' => ['required'],
                    ],
                ],
                'update_or_create' => ['so_num', 'sku'],
            ],
        ],
    ],
    'products' => [
        'label' => 'Import Products',
        'permission_required' => 'import-products',
        'files' => [
            'products' => [
                'label' => 'Product File',
                'headers_to_db' => [
                    'product_name' => [
                        'label' => 'Product Name',
                        'type' => 'string',
                        'validation' => ['required'],
                    ],
                    'sku' => [
                        'label' => 'SKU',
                        'type' => 'string',
                        'validation' => ['required', 'unique' => ['table' => 'products', 'column' => 'sku']],
                    ],
                    'price' => [
                        'label' => 'Price',
                        'type' => 'float',
                        'validation' => ['required'],
                    ],
                    'stock_quantity' => [
                        'label' => 'Stock Quantity',
                        'type' => 'integer',
                        'validation' => ['required'],
                    ],
                ],
                'update_or_create' => ['sku'],
            ],
        ],
    ],
    'customers_and_transactions' => [
        'label' => 'Import Customers and Transactions',
        'permission_required' => 'import-customers-transactions',
        'files' => [
            'customers' => [
                'label' => 'Customer File',
                'headers_to_db' => [
                    'customer_id' => [
                        'label' => 'Customer ID',
                        'type' => 'string',
                        'validation' => ['required', 'unique' => ['table' => 'customers', 'column' => 'customer_id']],
                    ],
                    'name' => [
                        'label' => 'Name',
                        'type' => 'string',
                        'validation' => ['required'],
                    ],
                    'email' => [
                        'label' => 'Email',
                        'type' => 'string',
                        'validation' => ['required', 'unique' => ['table' => 'customers', 'column' => 'email']],
                    ],
                    'phone' => [
                        'label' => 'Phone',
                        'type' => 'string',
                        'validation' => ['required'],
                    ],
                ],
                'update_or_create' => ['customer_id', 'email'],
            ],
            'transactions' => [
                'label' => 'Transaction File',
                'headers_to_db' => [
                    'transaction_id' => [
                        'label' => 'Transaction ID',
                        'type' => 'string',
                        'validation' => ['required', 'unique' => ['table' => 'transactions', 'column' => 'transaction_id']],
                    ],
                    'customer_id' => [
                        'label' => 'Customer ID',
                        'type' => 'string',
                        'validation' => ['required', 'exists' => ['table' => 'customers', 'column' => 'customer_id']],
                    ],
                    'amount' => [
                        'label' => 'Amount',
                        'type' => 'float',
                        'validation' => ['required'],
                    ],
                    'transaction_date' => [
                        'label' => 'Transaction Date',
                        'type' => 'date',
                        'validation' => ['required'],
                    ],
                ],
                'update_or_create' => ['transaction_id', 'customer_id'],
            ],
        ],
    ],
];