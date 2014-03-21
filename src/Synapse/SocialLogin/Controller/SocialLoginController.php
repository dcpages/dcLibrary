<?php

namespace Synapse\SocialLogin\Controller;

use LogicException;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Synapse\Controller\AbstractController;
use Synapse\SocialLogin\Exception\NoLinkedAccountException;
use Synapse\SocialLogin\LoginRequest;
use Synapse\SocialLogin\SocialLoginService;

use OAuth\ServiceFactory;
use OAuth\Common\Storage\Session as SessionStorage;
use OAuth\Common\Consumer\Credentials as ConsumerCredentials;

class SocialLoginController extends AbstractController
{
    protected $config;
    protected $service;
    protected $request;

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

    public function auth(Request $request)
    {
        $this->request = $request;
        $provider      = strtolower($request->attributes->get('provider'));

        if (! array_key_exists($provider, $this->serviceMap)
            || ! array_key_exists($provider, $this->config)) {
            return $this->createNotFoundResponse();
        }

        $service = $this->getServiceByProvider($provider);

        $redirectUri = $service->getAuthorizationUri();

        $response = new Response();
        $response->setStatusCode(301);
        $response->headers->set('Location', (string) $redirectUri);
        return $response;
    }

    public function callback(Request $request)
    {
        $this->request = $request;
        $provider      = strtolower($request->attributes->get('provider'));

        if (! array_key_exists($provider, $this->serviceMap)
            || ! array_key_exists($provider, $this->config)) {
            return $this->createNotFoundResponse();
        }

        // Check to see if this provider has a callback implemented
        if (! method_exists($this, $provider)) {
            throw new LogicException(sprintf(
                'Callback for provider \'%s\' not implemented',
                $provider
            ));
        }

        return $this->$provider($request, $provider);
    }

    protected function github(Request $request, $provider)
    {
        $code   = $request->query->get('code');
        $github = $this->getServiceByProvider('github');

        $token  = $github->requestAccessToken($code);

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

        try {
            $token = $this->service->handleLoginRequest($loginRequest);

            $redirect = $this->config['redirect-url'];
            $redirect .= '?'.http_build_query($token);
        } catch (NoLinkedAccountException $e) {
            $redirect = $this->config['redirect-url'];
            $redirect .= '?login_failure=1';
        }

        $response = new Response();
        $response->setStatusCode(301);
        $response->headers->set('Location', $redirect);
        return $response;
    }

    protected function facebook(Request $request, $provider)
    {
        $code     = $request->query->get('code');
        $facebook = $this->getServiceByProvider('facebook');

        $token  = $facebook->requestAccessToken($code);

        $user = json_decode($facebook->request('/me'), true);

        $loginRequest = new LoginRequest(
            'facebook',
            $user['id'],
            $token->getAccessToken(),
            $token->getEndOfLife() > 0 ? $token->getEndOfLife() : 0,
            $token->getRefreshToken(),
            [$user['email']]
        );

        try {
            $token = $this->service->handleLoginRequest($loginRequest);

            $redirect = $this->config['redirect-url'];
            $redirect .= '?'.http_build_query($token);
        } catch (NoLinkedAccountException $e) {
            $redirect = $this->config['redirect-url'];
            $redirect .= '?login_failure=1';
        }

        $response = new Response();
        $response->setStatusCode(301);
        $response->headers->set('Location', $redirect);
        return $response;
    }

    protected function getServiceByProvider($provider)
    {
        $redirect = $this->url($this->config[$provider]['callback_route'], array(
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

    public function setSocialLoginService(SocialLoginService $service)
    {
        $this->service = $service;
        return $this;
    }

    public function setConfig(array $config = array())
    {
        $this->config = $config;
        return $this;
    }
}
