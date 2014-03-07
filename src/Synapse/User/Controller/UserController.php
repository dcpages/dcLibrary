<?php

namespace Synapse\User\Controller;

use Symfony\Component\HttpFoundation\Request;
use Synapse\Controller\AbstractRestController;
use Synapse\User\UserService;

class UserController extends AbstractRestController
{
    protected $userService;

    public function post(Request $request)
    {
        $user = $this->content;

        if (! isset($user['email'], $user['password'])) {
            return $this->getSimpleResponse(422, 'Missing required field');
        }

        $this->userService->register($user);

        return array('id' => 1);
    }

    public function setUserService(UserService $service)
    {
        $this->userService = $service;
        return $this;
    }
}
