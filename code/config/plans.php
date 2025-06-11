<?php

return [
    'free' => [
        'max_tokens'  => 1,
        'daily_limit' => env('FREE_DAILY_LIMIT', 100),
    ],
    'pro' => [
        'max_tokens'  => 5,
        'daily_limit' => env('PRO_DAILY_LIMIT', 1000),
    ],
];
