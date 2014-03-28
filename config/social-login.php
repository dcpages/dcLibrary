<?php

use OAuth\OAuth2\Service\Facebook;
use OAuth\OAuth2\Service\Google;

/**
 * Settings
 * ========
 *
 * redirect-url The URL users will be redirected to after the callback handles the response of the OAuth provider
 *
 * provider     (See below for list of available providers)
 *
 *     key            The client id of the application registered with this OAuth provider
 *
 *     secret         The client secret of the application registered with this OAuth provider
 *
 *     callback_route The route of this application to specify as the callback for this OAuth request
 *
 *     scope          An array of scopes to request access to from the social login provider.
 *                    See OAuth\OAuth2\Service\* where * is the name of the provider. Each class contain a
 *                    constant for each scope option.
 *
 * Available Providers:
 *     'amazon'
 *     'bitbucket'
 *     'bitly'
 *     'box'
 *     'dailymotion'
 *     'dropbox'
 *     'etsy'
 *     'facebook'
 *     'fitbit'
 *     'flickr'
 *     'github'
 *     'google'
 *     'harvest'
 *     'heroku'
 *     'instagram'
 *     'linkedin'
 *     'mailchimp'
 *     'microsoft'
 *     'paypal'
 *     'reddit'
 *     'runkeeper'
 *     'salesforce'
 *     'soundcloud'
 *     'tumblr'
 *     'twitter'
 *     'vkontakte'
 *     'xing'
 *     'yammer'
 */

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
