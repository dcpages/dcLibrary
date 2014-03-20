<?php

namespace Synapse\Controller;

use Synapse\User\UserService;

use Symfony\Component\HttpFoundation\Request;

use OAuth2\HttpFoundationBridge\Response as BridgeResponse;
use OAuth2\HttpFoundationBridge\Request as OAuthRequest;
use OAuth2\Server as OAuth2Server;

class OAuthController
{
    protected $server;
    protected $userService;

    public function __construct(OAuth2Server $server, UserService $userService)
    {
        $this->server      = $server;
        $this->userService = $userService;
    }

    public function authorize(Request $request)
    {
        $response     = new BridgeResponse;
        $oauthRequest = OAuthRequest::createFromRequest($request);
        $authorized   = (bool) $request->get('authorize');

        return $this->server->handleAuthorizeRequest($oauthRequest, $response, $authorized);
    }

    public function token(Request $request)
    {
        $bridgeResponse = new BridgeResponse;
        $oauthRequest   = OAuthRequest::createFromRequest($request);

        $response = $this->server->handleTokenRequest($oauthRequest, $bridgeResponse);

        $userId = $response->getParameter('user_id');

        $this->setLastLogin($userId);

        return $response;
    }

    protected function setLastLogin($userId)
    {
        $user = $this->userService->findById($userId);

        $result = $this->userService->update($user, [
            'last_login' => time()
        ]);
    }
}
