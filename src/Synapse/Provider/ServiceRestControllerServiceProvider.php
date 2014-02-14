<?php

namespace Synapse\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class ServiceRestControllerServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['resolver'] = $app->share($app->extend('resolver', function ($resolver, $app) {
            return new ServiceRestControllerResolver($resolver, $app);
        }));
    }

    public function boot(Application $app)
    {
        // noop
    }
}
