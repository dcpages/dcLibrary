<?php

namespace Synapse\User;

use Synapse\User\Controller\UserController;
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

        $app['user.service'] = $app->share(function () use ($app) {
            $service = new UserService;
            $service->setUserMapper($app['user.mapper']);
            return $service;
        });

        $app['user.controller'] = $app->share(function () use ($app) {
            $controller = new UserController();
            $controller->setUserService($app['user.service']);
            return $controller;
        });

        $app->match('/users', 'user.controller:rest')
            ->method('HEAD|POST')
            ->bind('user-collection');

        $app->match('/users/{id}', 'user.controller:rest')
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
