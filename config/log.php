<?php

return [
    'loggly' => [
        'enable' => true,
        'token'  => null,
    ],
    'rollbar' => [
        'enable'                        => true,
        'post_server_item_access_token' => null,
        'root'                          => APPDIR,
    ],
    'file' => [
        'path' => APPDIR.'/app.log'
    ],
];
