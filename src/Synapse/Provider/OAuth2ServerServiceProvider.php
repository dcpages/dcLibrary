<?php

namespace Synapse\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Synapse\Controller\OAuthController;

use OAuth2\HttpFoundationBridge\Response as BridgeResponse;
use OAuth2\Server as OAuth2Server;
use OAuth2\Storage\Pdo as OAuth2Pdo;
use OAuth2\GrantType\AuthorizationCode;
use OAuth2\GrantType\UserCredentials;

class OAuth2ServerServiceProvider implements ServiceProviderInterface
{
    public function setup(Application $app)
    {
        // Get the PDO object from Zend\Db
        $pdo = $app['db']->getDriver()->getConnection()->getResource();

        // Create the storage object
        $storage = new OAuth2Pdo($pdo);

        $grantTypes = [
            // @todo may want to implement this so that tools like postman are
            // easier to use
            // 'authorization_code' => new AuthorizationCode($storage),
            'user_credentials'   => new UserCredentials($storage),
        ];

        $server = new OAuth2Server($storage, [
            'enforce_state'  => true,
            'allow_implicit' => true,
        ], $grantTypes);

        $app['oauth_server'] = $server;

        $app['oauth.controller'] = $app->share(function () use ($app) {
            return new OAuthController($app['oauth_server']);
        });
    }

    public function register(Application $app)
    {
        $this->setup($app);

        $app->post('/authorize', 'oauth.controller:authorize');
        $app->post('/token', 'oauth.controller:token');
    }

    public function boot(Application $app)
    {
        // no op
    }
}
