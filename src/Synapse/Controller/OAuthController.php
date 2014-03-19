<?php

namespace Synapse\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use OAuth2\HttpFoundationBridge\Response as BridgeResponse;
use OAuth2\HttpFoundationBridge\Request as OAuthRequest;
use OAuth2\Server as OAuth2Server;

use Synapse\Application\SecurityAwareInterface;
use Synapse\Application\SecurityAwareTrait;
use Synapse\OAuth2\Mapper\AccessToken as AccessTokenMapper;
use Synapse\OAuth2\Mapper\RefreshToken as RefreshTokenMapper;

class OAuthController implements SecurityAwareInterface
{
    use SecurityAwareTrait;

    protected $server;
    protected $accessTokenMapper;
    protected $refreshTokenMapper;

    public function __construct(
        OAuth2Server $server,
        AccessTokenMapper $accessTokenMapper,
        RefreshTokenMapper $refreshTokenMapper
    ) {
        $this->server             = $server;
        $this->accessTokenMapper  = $accessTokenMapper;
        $this->refreshTokenMapper = $refreshTokenMapper;
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

    public function logout(Request $request)
    {
        $accessToken = $this->security->getToken()->getOAuthToken();

        if (! $accessToken) {
            return new Response('Authentication required', 401);
        }

        $token = $this->accessTokenMapper->findBy([
            'access_token' => $accessToken
        ]);

        $token->setExpires(date("Y-m-d H:i:s", time()));

        $this->accessTokenMapper->update($token);

        return new Response('', 200);
    }
}
