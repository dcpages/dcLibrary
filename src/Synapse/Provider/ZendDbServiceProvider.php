<?php

namespace Synapse\Provider;

use Synapse\Db\TableGatewayFactory as TableGatewayFactory;
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

        $app['tableGatewayFactory'] = $app->share(function () use ($app) {
            return new TableGatewayFactory($app['db']);
        });
    }

    public function boot(Application $app)
    {
        // noop
    }
}
