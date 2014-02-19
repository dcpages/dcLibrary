<?php

namespace Synapse\Controller;

use Symfony\Component\HttpFoundation\Request;

use OAuth2\HttpFoundationBridge\Response as BridgeResponse;
use OAuth2\HttpFoundationBridge\Request as OAuthRequest;
use OAuth2\Server as OAuth2Server;

class OAuthController
{
    protected $server;

    public function __construct(OAuth2Server $server)
    {
        $this->server = $server;
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
        $response     = new BridgeResponse;
        $oauthRequest = OAuthRequest::createFromRequest($request);

        return $this->server->handleTokenRequest($oauthRequest, $response);
    }
}
