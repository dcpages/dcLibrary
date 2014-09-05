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
 * whitelist  Only send emails to the addresses OR domains specified (if an address matches either whitelist, it is considered valid)
 *     list Array of valid domain names and email addresses (null = all are valid; empty array = none are valid)
 *     trap Email address to which emails are sent if they don't match the whitelist
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
    'whitelist' => [
        'list' => null,
        'trap' => null,
    ],
];
