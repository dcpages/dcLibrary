<?php

use OAuth\OAuth2\Service\Facebook;

return array(
    'redirect-url' => 'http://127.0.0.1:9000/receive-token',
    'github' => array(
        'key'                  => '',
        'secret'               => '',
        'login_callback_route' => 'social-login-callback',
        'link_callback_route'  => 'social-link-callback',
        'scope'                => array('user'),
    ),
    'facebook' => array(
        'key'                  => '',
        'secret'               => '',
        'login_callback_route' => 'social-login-callback',
        'link_callback_route'  => 'social-link-callback',
        'scope'                => array(Facebook::SCOPE_EMAIL, Facebook::SCOPE_READ_FRIENDLIST),
    ),
);
