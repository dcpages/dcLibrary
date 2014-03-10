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
        $token = $request->attributes->get('token');

        $conditions = [
            'user_id' => $id,
            'token'   => $token,
        ];

        $token = $this->userService
            ->findTokenBy($conditions);

        try {
            $user = $this->userService->verifyRegistration($token);
        } catch (OutOfBoundsException $e) {
            return $this->getSimpleResponse(500, $e->getMessage());
        }

        return $user;
    }

    public function setUserService(UserService $service)
    {
        $this->userService = $service;
        return $this;
    }
}
