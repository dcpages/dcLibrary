<?php

namespace Synapse\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class ConfigServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['resolver'] = $app->share($app->extend('resolver', function ($resolver, $app) {
            return new ConfigServiceResolver($resolver, $app);
        }));
    }

    public function boot(Application $app)
    {
        // noop
    }
}
