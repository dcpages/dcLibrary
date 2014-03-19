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
use Synapse\Stdlib\Arr;

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
        $content = json_decode($request->getContent(), true);

        $securityToken = $this->security->getToken();
        $user          = $securityToken->getUser();
        $accessToken   = $securityToken->getOAuthToken();
        $refreshToken  = Arr::get($content, 'refresh_token');

        if (! $accessToken) {
            return new Response('Authentication required', 401);
        }

        if (! $refreshToken) {
            return new Response('Refresh token not provided', 422);
        }

        try {
            $this->expireAccessToken($accessToken);
            $this->expireRefreshToken($refreshToken, $user);
        } catch (OutOfBoundsException $e) {
            return new Response($e->getMessage(), 422);
        }

        return new Response('', 200);
    }

    protected function expireAccessToken($accessToken)
    {
        // Expire access token
        $token = $this->accessTokenMapper->findBy([
            'access_token' => $accessToken
        ]);

        if (! $token) {
            throw new OutOfBoundsException('Access token not found.');
        }

        $token->setExpires(date("Y-m-d H:i:s", time()));

        $this->accessTokenMapper->update($token);
    }

    protected function expireRefreshToken($refreshToken, $user)
    {
        $token = $this->refreshTokenMapper->findBy([
            'refresh_token' => $refreshToken,
            'user_id'       => $user->getId(),
        ]);

        if (! $token) {
            throw new OutOfBoundsException('Refresh token not found.');
        }

        $token->setExpires(date("Y-m-d H:i:s", time()));

        $this->refreshTokenMapper->update($token);
    }
}
