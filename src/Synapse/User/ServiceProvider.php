<?php

namespace Synapse\User;

use Synapse\User\Entity\User as UserEntity;
use Synapse\User\Mapper\User as UserMapper;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Service provider for user related services
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Register services related to Users
     *
     * @param  Application $app
     */
    public function register(Application $app)
    {
        $app['user.mapper'] = $app->share(function () use ($app) {
            return new UserMapper($app['db'], new UserEntity);
        });

        $app['user.controller'] = $app->share(function () use ($app) {
            return new UserController();
        });

        $app->match('/user', 'user.controller:rest')
            ->method('HEAD|POST')
            ->bind('user-collection');

        $app->match('/user/{id}', 'user.controller:rest')
            ->method('GET|PUT')
            ->bind('user-entity');
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
