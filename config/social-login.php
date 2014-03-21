<?php

use OAuth\OAuth2\Service\Facebook;

return array(
    'redirect-url' => 'http://127.0.0.1:9000/receive-token',
    'github' => array(
        'key'            => '',
        'secret'         => '',
        'callback_route' => 'social-login-callback',
        'scope'          => array('user'),
    ),
    'facebook' => array(
        'key'            => '',
        'secret'         => '',
        'callback_route' => 'social-login-callback',
        'scope'          => array(Facebook::SCOPE_EMAIL, Facebook::SCOPE_READ_FRIENDLIST),
    ),
);
