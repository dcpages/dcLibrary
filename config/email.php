<?php

return [
    'sender' => [
        'mandrill' => [
            'apiKey' => null,
        ],
    ],
    'defaults' => [
        'sender' => [
            'email' => 'no-reply@example.com',
            'name'  => 'Example User',
        ],
        'headers' => [
            'Reply-To' => [
                'email' => 'me@example.com',
                'name'  => 'Example Reply-To',
            ],
        ],
    ],

];
