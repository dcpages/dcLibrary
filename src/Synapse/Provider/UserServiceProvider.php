<?php

namespace Synapse\Provider;

use Synapse\Entity\User as UserEntity;
use Synapse\Mapper\User as UserMapper;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Service provider for user related services
 */
class UserServiceProvider implements ServiceProviderInterface
{
    /**
     * Register services related to Users
     *
     * @param  Application $app
     */
    public function register(Application $app)
    {
        $app['user.entity'] = function () {
            return new UserEntity;
        };

        $app['user.mapper'] = function () {
            return new UserMapper;
        }
    }

    /**
     * Perform chores on boot. (None required here.)
     *
     * @param  Application $app
     */
    public function boot()
    {
        // noop
    }
}
