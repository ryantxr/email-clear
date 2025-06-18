<?php

return [
    'free' => [
        'max_tokens'   => 1,
        'monthly_limit' => env('FREE_MONTHLY_LIMIT', 100),
    ],
    'pro' => [
        'max_tokens'   => 5,
        'monthly_limit' => env('PRO_MONTHLY_LIMIT', 1000),
    ],
];
