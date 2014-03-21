<?php

namespace Synapse\SocialLogin\Controller;

use LogicException;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Synapse\Controller\AbstractController;
use Synapse\SocialLogin\Exception\NoLinkedAccountException;
use Synapse\SocialLogin\Exception\LinkedAccountExistsException;
use Synapse\SocialLogin\LoginRequest;
use Synapse\SocialLogin\SocialLoginService;

use OAuth\ServiceFactory;
use OAuth\Common\Storage\Session as SessionStorage;
use OAuth\Common\Consumer\Credentials as ConsumerCredentials;
use OAuth\OAuth2\Service as Oauth2Service;
use OAuth\Common\Token\TokenInterface;

use OutOfBoundsException;

/**
 * Controller for logging in with a social account
 *
 * In this context, a "social account" is really any separate OAuth provider in $serviceMap
 */
class SocialLoginController extends AbstractController
{
    /**
     * Constants for the type of action being performed
     */
    const ACTION_LOGIN_WITH_ACCOUNT = 1;
    const ACTION_LINK_ACCOUNT       = 2;

    /**
     * Social login configuration
     *
     * @var array
     */
    protected $config;

    /**
     * Social login service
     *
     * @var SocialLoginService
     */
    protected $service;

    /**
     * Map of service keys to names
     *
     * @var array
     */
    protected $serviceMap = array(
        'amazon'      => 'Amazon',
        'bitbucket'   => 'BitBucket',
        'bitly'       => 'Bitly',
        'box'         => 'Box',
        'dailymotion' => 'Dailymotion',
        'dropbox'     => 'Dropbox',
        'etsy'        => 'Etsy',
        'facebook'    => 'Facebook',
        'fitbit'      => 'FitBit',
        'flickr'      => 'Flickr',
        'github'      => 'GitHub',
        'google'      => 'Google',
        'harvest'     => 'Harvest',
        'heroku'      => 'Heroku',
        'instagram'   => 'Instagram',
        'linkedin'    => 'Linkedin',
        'mailchimp'   => 'Mailchimp',
        'microsoft'   => 'Microsoft',
        'paypal'      => 'Paypal',
        'reddit'      => 'Reddit',
        'runkeeper'   => 'RunKeeper',
        'salesforce'  => 'Salesforce',
        'soundcloud'  => 'SoundCloud',
        'tumblr'      => 'Tumblr',
        'twitter'     => 'Twitter',
        'vkontakte'   => 'Vkontakte',
        'xing'        => 'Xing',
        'yammer'      => 'Yammer',
    );

    /**
     * @param SocialLoginService $service
     */
    public function setSocialLoginService(SocialLoginService $service)
    {
        $this->service = $service;
        return $this;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config = array())
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Authenticate via a separate OAuth provider with the intent to login
     *
     * @param  Request $request
     * @return Response
     */
    public function login(Request $request)
    {
        return $this->auth($request, self::ACTION_LOGIN_WITH_ACCOUNT);
    }

    /**
     * Authenticate via a separate OAuth provider with the intent to link
     * the social account to an already established
     *
     * @param  Request $request
     * @return Response
     */
    public function link(Request $request)
    {
        return $this->auth($request, self::ACTION_LINK_ACCOUNT);
    }

    /**
     * Callback for OAuth authentication requests to login via social account
     *
     * Passes request with provider token to the appropriate provider method and logs in the user
     *
     * @param  Request $request
     * @return Response
     */
    public function loginCallback(Request $request)
    {
        return $this->callback($request, self::ACTION_LOGIN_WITH_ACCOUNT);
    }

    /**
     * Callback for OAuth authentication requests to link account to social account
     *
     * @param  Request $request
     * @return Response
     */
    public function linkCallback(Request $request)
    {
        return $this->callback($request, self::ACTION_LINK_ACCOUNT);
    }

    /**
     * Authenticate via a separate OAuth provider
     *
     * @param  Request $request
     * @param  int     $action  Constant representing either Login or Link account
     * @return Response
     */
    public function auth(Request $request, $action)
    {
        $provider = strtolower($request->attributes->get('provider'));

        if (! $this->providerExists($provider)) {
            return $this->createNotFoundResponse();
        }

        $service = $this->getServiceByProvider($provider, $action);

        $redirectUri = $service->getAuthorizationUri();

        $response = new Response();
        $response->setStatusCode(301);
        $response->headers->set('Location', (string) $redirectUri);
        return $response;
    }

    /**
     * Callback for OAuth authentication requests
     *
     * @param  Request  $request
     * @param  int      $action   Constant representing either Login or Link account
     * @return Response
     */
    protected function callback(Request $request, $action)
    {
        // Check to see if this provider exists and has a callback implemented
        $provider = strtolower($request->attributes->get('provider'));

        if (! $this->providerExists($provider)) {
            return $this->createNotFoundResponse();
        }

        if (! method_exists($this, $provider)) {
            throw new LogicException(sprintf(
                'Callback for provider \'%s\' not implemented',
                $provider
            ));
        }

        // Use provider service and access token from provider to create a LoginRequest for our app
        $code            = $request->query->get('code');
        $providerService = $this->getServiceByProvider($provider, $action);
        $providerToken   = $providerService->requestAccessToken($code);

        $socialLoginRequest = $this->$provider($providerService, $providerToken);

        // Handle login or link-account request and redirect to the redirect-url
        try {
            if ($action === self::ACTION_LOGIN_WITH_ACCOUNT) {
                $token = $this->service->handleLoginRequest($socialLoginRequest);
            } elseif ($action === self::ACTION_LINK_ACCOUNT) {
                $token = $this->service->handleLinkRequest($socialLoginRequest);
            }

            $redirect = $this->config['redirect-url'];
            $redirect .= '?'.http_build_query($token);
        } catch (NoLinkedAccountException $e) {
            $redirect = $this->config['redirect-url'];
            $redirect .= '?login_failure=1&error=no_linked_account';
        } catch (LinkedAccountExistsException $e) {
            $redirect = $this->config['redirect-url'];
            $redirect .= '?login_failure=1&error=account_already_linked';
        } catch (OutOfBoundsException $e) {
            $redirect = $this->config['redirect-url'];
            $redirect .= '?login_failure=1';

            if ($e->getCode() === UserService::EXCEPTION_ACCOUNT_NOT_FOUND) {
                $redirect .= '&error=account_already_linked';
            }
        }

        $response = new Response();
        $response->setStatusCode(301);
        $response->headers->set('Location', $redirect);

        return $response;
    }

    /**
     * Determine whether the provider exists in the service map as well as the social login config
     *
     * @param  string $provider
     * @return bool
     */
    protected function providerExists($provider)
    {
        if (! array_key_exists($provider, $this->serviceMap)) {
            return false;
        }

        if (! array_key_exists($provider, $this->config)) {
            return false;
        }

        return true;
    }

    /**
     * Request access token from GitHub and return a LoginRequest object for logging into our app
     *
     * @param  Oauth2Service\GitHub $github
     * @param  TokenInterface       $token
     * @return LoginRequest
     */
    protected function github(Oauth2Service\GitHub $github, TokenInterface $token)
    {
        $emails = json_decode($github->request('user/emails'), true);
        $user   = json_decode($github->request('user'), true);

        $loginRequest = new LoginRequest(
            'github',
            $user['id'],
            $token->getAccessToken(),
            $token->getEndOfLife() > 0 ? $token->getEndOfLife() : 0,
            $token->getRefreshToken(),
            $emails
        );

        return $loginRequest;
    }

    /**
     * Request access token from Facebook and return a LoginRequest object for logging into our app
     *
     * @param  Oauth2Service\Facebook $facebook
     * @param  TokenInterface         $token
     * @return LoginRequest
     */
    protected function facebook(Oauth2Service\Facebook $facebook, TokenInterface $token)
    {
        $user = json_decode($facebook->request('/me'), true);

        $loginRequest = new LoginRequest(
            'facebook',
            $user['id'],
            $token->getAccessToken(),
            $token->getEndOfLife() > 0 ? $token->getEndOfLife() : 0,
            $token->getRefreshToken(),
            [$user['email']]
        );

        return $loginRequest;
    }

    /**
     * Get a provider service given a provider name
     *
     * @param  string $provider
     * @param  int    $action   Constant representing Login or Link account (to determine which callback to use)
     * @return OAuth\Common\Service\ServiceInterface
     */
    protected function getServiceByProvider($provider, $action)
    {
        if ($action === self::ACTION_LOGIN_WITH_ACCOUNT) {
            $route = 'login_callback_route';
        } elseif ($action === self::ACTION_LINK_ACCOUNT) {
            $route = 'link_callback_route';
        }

        $redirect = $this->url($this->config[$provider][$route], array(
            'provider' => $provider,
        ));

        $serviceName = $this->serviceMap[$provider];
        $storage     = new SessionStorage();
        $credentials = new ConsumerCredentials(
            $this->config[$provider]['key'],
            $this->config[$provider]['secret'],
            $redirect
        );

        $serviceFactory = new ServiceFactory;
        $service = $serviceFactory->createService(
            $serviceName,
            $credentials,
            $storage,
            $this->config[$provider]['scope']
        );

        return $service;
    }
}
