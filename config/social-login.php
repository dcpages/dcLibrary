<?php

use OAuth\OAuth2\Service\Facebook;
use OAuth\OAuth2\Service\Google;

return array(
    'redirect-url' => 'http://127.0.0.1:9000/receive-token',

    'facebook' => array(
        'key'            => '',
        'secret'         => '',
        'callback_route' => 'social-login-callback',
        'scope'          => array(Facebook::SCOPE_EMAIL, Facebook::SCOPE_READ_FRIENDLIST),
    ),

    'github' => array(
        'key'            => '',
        'secret'         => '',
        'callback_route' => 'social-login-callback',
        'scope'          => array('user'),
    ),

    'google' => array(
        'key'            => '',
        'secret'         => '',
        'callback_route' => 'social-login-callback',
        'scope'          => [Google::SCOPE_USERINFO_EMAIL, Google::SCHOPE_USERINFO_PROFILE],
    );

);
