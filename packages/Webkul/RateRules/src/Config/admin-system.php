<?php

return [
    /**
     * General.
     */
    [
        'key'    => 'sales.carriers.standardrate',
        'name'   => 'Livraison à tarif variable',
        'info'   => "Des frais variables sont facturés pour l'expédition, en fonction du poids et de la distance du colis.",
        'sort'   => 0,
        'fields' => [
            [
                'name'          => 'description',
                'title'         => 'admin::app.configuration.index.sales.shipping-methods.flat-rate-shipping.description',
                'type'          => 'textarea',
                'channel_based' => true,
                'locale_based'  => true,
            ], [
                'name'          => 'active',
                'title'         => 'admin::app.configuration.index.sales.shipping-methods.flat-rate-shipping.status',
                'type'          => 'boolean',
                'channel_based' => true,
                'locale_based'  => false,
            ],
        ],
    ]
];
