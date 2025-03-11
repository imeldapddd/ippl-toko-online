<?php

return [
    'name' => 'Shop',
    
    // Tambahkan konfigurasi views
    'views' => [
        'paths' => [
            resource_path('modules/shop/views'),
        ],
        'namespaces' => [
            'shop' => resource_path('modules/shop/views'),
        ],
    ],
];
