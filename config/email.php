<?php

/**
 * Settings
 * ========
 *
 * sender   Contains a list of senders
 * defaults Default options for sending emails
 *     sender
 *         email Default email address for sender
 *         name  Default name for sender
 *     headers
 *         Reply-To
 *             email Default email address for the Reply-To header (hitting reply replies to this address)
 *             name  Default name of reply-to person
 */

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
