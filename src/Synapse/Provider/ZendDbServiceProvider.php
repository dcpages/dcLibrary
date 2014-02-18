<?php

namespace Synapse\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Zend\Db\Adapter\Adapter;

class ZendDbServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['db'] = $app->share(function () use ($app) {
            return new Adapter($app['config']->load('db'));
        });
    }

    public function boot(Application $app)
    {
        // noop
    }
}
