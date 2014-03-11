<?php

namespace Synapse\User\Controller;

use Symfony\Component\HttpFoundation\Request;
use Synapse\Controller\AbstractRestController;
use Synapse\User\UserService;
use Synapse\User\Entity\UserToken;
use OutOfBoundsException;

class VerifyRegistrationController extends AbstractRestController
{
    protected $userService;

    public function post(Request $request)
    {
        $id    = $request->attributes->get('id');
        $token = $request->request->get('token');

        $conditions = [
            'user_id' => $id,
            'token'   => $token,
        ];

        $token = $this->userService->findTokenBy($conditions);

        if (! $token) {
            return $this->getSimpleResponse(404, 'Token not found.');
        }

        try {
            $user = $this->userService->verifyRegistration($token);
        } catch (OutOfBoundsException $e) {
            return $this->getSimpleResponse($e->getCode(), $e->getMessage());
        }

        $user = $user->getArrayCopy();

        unset($user['password']);

        return $user;
    }

    public function setUserService(UserService $service)
    {
        $this->userService = $service;
        return $this;
    }
}
