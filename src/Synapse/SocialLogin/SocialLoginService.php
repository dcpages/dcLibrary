<?php

namespace Synapse\SocialLogin;

use Synapse\User\Entity\User as UserEntity;
use Synapse\User\UserService as UserService;
use Synapse\OAuth2\ResponseType\AccessToken;
use Synapse\OAuth2\Storage\ZendDb as OAuth2ZendDb;
use Synapse\SocialLogin\Exception\NoLinkedAccountException;
use Synapse\SocialLogin\Exception\LinkedAccountExistsException;
use OutOfBoundsException;

class SocialLoginService
{
    const EXCEPTION_ACCOUNT_NOT_FOUND = 1;

    protected $userService;
    protected $socialLoginMapper;
    protected $tokenStorage;

    public function handleLoginRequest(LoginRequest $request)
    {
        $socialLogin = $this->socialLoginMapper->findByProviderUserId(
            $request->getProvider(),
            $request->getProviderUserId()
        );

        if ($socialLogin) {
            $user = $this->userService->findById(
                $socialLogin->getUserId()
            );

            return $this->handleLogin($user, $request);
        }

        $userFound = false;
        foreach ($request->getEmails() as $email) {
            $user = $this->userService->findByEmail($email);

            if ($user) {
                $userFound = true;
                if ($this->userHasSocialLoginWithProvider($request->getProvider(), $user)) {
                    return $this->handleLogin($user, $request);
                }
            }
        }

        if ($userFound) {
            throw new NoLinkedAccountException;
        }

        $result = $this->registerFromSocialLogin($request);
        return $this->handleLogin($result['user'], $request);
    }

    public function handleLinkRequest(LoginRequest $request, $userId)
    {
        $socialLogin = $this->socialLoginMapper->findByProviderUserId(
            $request->getProvider(),
            $request->getProviderUserId()
        );

        if ($socialLogin) {
            throw new LinkedAccountExistsException;
        }

        $user = $this->userService->findById($userId);

        if (! $user) {
            throw new OutOfBoundsException('Account not found', self::EXCEPTION_ACCOUNT_NOT_FOUND);
        }

        $socialLoginEntity = new SocialLoginEntity;
        $socialLoginEntity->setUserId($user->getId())
            ->setProvider($request->getProvider())
            ->setProviderUserId($request->getProviderUserId())
            ->setAccessToken($request->getAccessToken())
            ->setAccessTokenExpires($request->getAccessTokenExpires())
            ->setRefreshToken($request->getRefreshToken());

        $socialLogin = $this->socialLoginMapper->persist($socialLoginEntity);

        return $this->handleLogin($user, $request);
    }

    public function registerFromSocialLogin(LoginRequest $request)
    {
        $email = $request->getEmails()[0];
        $user  = $this->userService->registerWithoutPassword(array(
            'email' => $email
        ));

        $socialLoginEntity = new SocialLoginEntity;
        $socialLoginEntity->setUserId($user->getId())
            ->setProvider($request->getProvider())
            ->setProviderUserId($request->getProviderUserId())
            ->setAccessToken($request->getAccessToken())
            ->setAccessTokenExpires($request->getAccessTokenExpires())
            ->setRefreshToken($request->getRefreshToken());

        $entity = $this->socialLoginMapper->persist($socialLoginEntity);
        return array(
            'user'         => $user,
            'social_login' => $entity
        );
    }

    public function userHasSocialLoginWithProvider($provider, $user)
    {
        return (bool) $this->socialLoginMapper->findBy([
            'provider' => $provider,
            'user_id'  => $user->getId()
        ]);
    }

    public function handleLogin(UserEntity $user, LoginRequest $request)
    {
        $accessToken = new AccessToken($this->tokenStorage, $this->tokenStorage);
        $token = $accessToken->createAccessToken('', $user->getId(), null, true);

        return $token;
    }

    public function setOAuthStorage(OAuth2ZendDb $storage)
    {
        $this->tokenStorage = $storage;
        return $this;
    }

    public function setSocialLoginMapper(SocialLoginMapper $mapper)
    {
        $this->socialLoginMapper = $mapper;
        return $this;
    }

    public function setUserService(UserService $service)
    {
        $this->userService = $service;
        return $this;
    }
}
