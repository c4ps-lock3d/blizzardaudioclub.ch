<?php

return [
    'stripe'  => [
        'code'        => 'stripe',
        'title'       => 'Stripe',
        'description' => 'Stripe',
        'image'       => '/images/stripe.svg',
        'class'       => 'Webkul\Stripe\Payment\Stripe',
        'active'      => true,
        'sort'        => 0,
    ],
    'stripetwint'  => [
        'code'        => 'stripetwint',
        'title'       => 'StripeTwint',
        'description' => 'StripeTwint',
        'image'       => '/images/stripe.svg',
        'class'       => 'Webkul\Stripe\Payment\StripeTwint',
        'active'      => true,
        'sort'        => 1,
    ]
];
