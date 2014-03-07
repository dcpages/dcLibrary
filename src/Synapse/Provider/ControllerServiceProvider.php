<?php

namespace Synapse\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class ControllerServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['resolver'] = $app->share($app->extend('resolver', function ($resolver, $app) {
            return new ControllerResolver($resolver, $app);
        }));
    }

    /**
     * Perform chores on boot. (None required here.)
     *
     * @param  Application $app
     */
    public function boot(Application $app)
    {
        // noop
    }
}