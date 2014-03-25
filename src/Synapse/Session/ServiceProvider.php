<?php

namespace Synapse\Session;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Synapse\Session\RedisSessionHandler;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['session'] = $app->share(function ($app) {
            $app['session.storage'] = new NativeSessionStorage();

            return new Session($app['session.storage']);
        });

        $app['session.storage.handler'] = $app->share(function () {
            return new RedisSessionHandler(new Redis());
        });

        $app['session.storage.options'] = array();
        $app['session.default_locale'] = 'en';
        $app['session.storage.save_path'] = null;
    }

    public function boot(Application $app)
    {
        $app['session']->start();
    }
}
