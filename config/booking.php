<?php

return [

    'slot_interval_minutes' => 15,

    'plans' => [
        'free' => [
            'name' => 'Free',
            'staff_limit' => 1,
            'price_cents' => 0,
            'stripe_price' => env('STRIPE_PRICE_FREE'),
            'features' => ['1 staff member', 'Public booking page', 'Email confirmations'],
        ],
        'pro' => [
            'name' => 'Pro',
            'staff_limit' => 5,
            'price_cents' => 2900,
            'stripe_price' => env('STRIPE_PRICE_PRO'),
            'features' => ['Up to 5 staff', 'Telegram alerts', 'No-show tracking'],
        ],
        'business' => [
            'name' => 'Business',
            'staff_limit' => null,
            'price_cents' => 7900,
            'stripe_price' => env('STRIPE_PRICE_BUSINESS'),
            'features' => ['Unlimited staff', 'Priority support', 'Advanced stats'],
        ],
    ],

];
