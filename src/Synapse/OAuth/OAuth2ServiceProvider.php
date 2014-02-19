<?php

namespace Synapse\OAuth;

use RuntimeException;

use Synapse\OAuth\OAuth2Server\SessionManager;
use Synapse\OAuth\OAuth2Server\ClientManager;
use Synapse\OAuth\OAuth2Server\ScopeManager;

use League\OAuth2\AuthServer;
use League\OAuth2\ResourceServer;
use League\OAuth2\Grant\Password as PasswordGrantType;
use League\OAuth2\Grant\AuthCode as AuthCodeGrantType;
use League\OAuth2\Grant\ClientCredentials as ClientCredentialsGrantType;
use League\OAuth2\Grant\RefreshToken as RefreshTokenGrantType;

use Silex\Application;
use Silex\ServiceProviderInterface;

class OAuth2ServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['oauth2.session_manager'] = $app->share(function () use ($app) {
            return new SessionManager($app['db']);
        });

        $app['oauth2.client_manager'] = $app->share(function () use ($app) {
            return new ClientManager($app['db']);
        });

        $app['oauth2.scope_manager'] = $app->share(function () use ($app) {
            return new ScopeManager($app['db']);
        });

        $app['oauth.resource_server'] = $app->share(function () use ($app) {
            return new ResourceServer($app['oauth2.session_manager']);
        });

        $app['oauth2.auth_server'] = $app->share(function () use ($app) {

            $authServer = new AuthServer(
                $app['oauth2.client_manager'],
                $app['oauth2.session_manager'],
                $app['oauth2.scope_manager']
            );

            $options = isset($app['oauth2.options']) ? $app['oauth2.options'] : [];

            if (array_key_exists('access_token_ttl', $options)) {
                $authServer->setExpiresIn($options['access_token_ttl']);
            }

            // Configure grant types.
            if (array_key_exists('grant_types', $options) && is_array($options['grant_types'])) {
                foreach ($app['oauth2.options']['grant_types'] as $type) {
                    switch ($type) {
                        case 'authorization_code':
                            $authServer->addGrantType(new AuthCodeGrantType());
                            break;
                        case 'client_credentials':
                            $authServer->addGrantType(new ClientCredentialsGrantType());
                            break;
                        case 'password':
                            if (!is_callable($options['password_verify_callback'])) {
                                throw new RuntimeException(
                                    'To use the OAuth2 "password" grant type, '
                                    . 'the "password_verify_callback" option '
                                    . 'must be set to a callback function.'
                                );
                            }
                            $grantType = new PasswordGrantType();
                            $grantType->setVerifyCredentialsCallback($options['password_verify_callback']);
                            $authServer->addGrantType($grantType);
                            break;
                        case 'refresh_token':
                            $authServer->addGrantType(new RefreshTokenGrantType());
                            break;
                        default:
                            throw new RuntimeException('Invalid grant type "' . $type . '" specified in oauth2.options.');
                    }
                }
            }

            return $authServer;
        });

    }

    public function boot(Application $app)
    {
    }
}
