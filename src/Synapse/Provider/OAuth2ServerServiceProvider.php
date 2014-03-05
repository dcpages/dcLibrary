<?php

namespace Synapse\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Synapse\Controller\OAuthController;
use Synapse\OAuth2\Storage\Pdo as OAuth2Pdo;

use OAuth2\HttpFoundationBridge\Response as BridgeResponse;
use OAuth2\Server as OAuth2Server;
use OAuth2\GrantType\AuthorizationCode;
use OAuth2\GrantType\UserCredentials;

class OAuth2ServerServiceProvider implements ServiceProviderInterface
{
    public function setup(Application $app)
    {
        $app['oauth.storage'] = $app->share(function () use ($app) {
            // Get the PDO object from Zend\Db
            $pdo = $app['db']->getDriver()->getConnection()->getResource();

            // Create the storage object
            $storage = new OAuth2Pdo($pdo);
            $storage->setUserMapper($app['user.mapper']);

            return $storage;
        });

        $app['oauth_server'] = $app->share(function () use ($app) {
            $storage = $app['oauth.storage'];

            $grantTypes = [
                // @todo may want to implement this so that tools like postman are
                // easier to use
                // 'authorization_code' => new AuthorizationCode($storage),
                'user_credentials'   => new UserCredentials($storage),
            ];

            return new OAuth2Server($storage, [
                'enforce_state'  => true,
                'allow_implicit' => true,
            ], $grantTypes);
        });

        $app['oauth.controller'] = $app->share(function () use ($app) {
            return new OAuthController($app['oauth_server']);
        });
    }

    public function register(Application $app)
    {
        $this->setup($app);

        $app->post('/oauth/authorize', 'oauth.controller:authorize');
        $app->post('/oauth/token', 'oauth.controller:token');
    }

    public function boot(Application $app)
    {
    }
}
