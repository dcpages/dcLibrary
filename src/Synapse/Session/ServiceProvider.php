<?php

namespace Synapse\Session;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Drak\NativeSession\NativeRedisSessionHandler;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['session.storage.handler'] = $app->share(function ($app) {
            return new NativeRedisSessionHandler;
        });
    }

    public function boot(Application $app)
    {
        // noop
    }
}
