<?php

namespace Synapse\Provider;

use Resque;
use Silex\Application;
use Silex\ServiceProviderInterface;

use Synapse\Command\Resque as ResqueCommand;

class ResqueServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['resque'] = $app->share(function () use ($app) {
            return new Resque($app['config']->load('resque'));
        });

        $app['resque.command'] = $app->share(function () use ($app) {
            $command = new ResqueCommand;
            $command->setResque($app['resque']);
            return $command;
        });

    }

    public function boot(Application $app)
    {
        $app->command('resque.command');
    }
}
