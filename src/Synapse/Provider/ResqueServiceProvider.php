<?php

namespace Synapse\Provider;

use Resque;
use Silex\Application;
use Silex\ServiceProviderInterface;

class ResqueServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['resque'] = $app->share(function () use ($app) {
            return new Resque($app['config']->load('resque'));
        });
    }

    public function boot(Application $app)
    {
        // noop
    }
}
