<?php

use OAuth\OAuth2\Service\Facebook;
use OAuth\OAuth2\Service\Google;

return [
    'redirect-url' => 'http://127.0.0.1:9000/receive-token',

    'facebook' => [
        'key'            => '',
        'secret'         => '',
        'callback_route' => 'social-login-callback',
        'scope'          => [Facebook::SCOPE_EMAIL, Facebook::SCOPE_READ_FRIENDLIST],
    ],

    'github' => [
        'key'            => '',
        'secret'         => '',
        'callback_route' => 'social-login-callback',
        'scope'          => ['user'],
    ],

    'google' => [
        'key'            => '',
        'secret'         => '',
        'callback_route' => 'social-login-callback',
        'scope'          => [Google::SCOPE_USERINFO_EMAIL, Google::SCOPE_USERINFO_PROFILE],
    ]

];
