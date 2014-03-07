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

        $newUser = $this->userService->register($user)
            ->getArrayCopy();

        unset($newUser['password']);

        $newUser['_href'] = $this->url('user-entity', array('id' => $newUser['id']));

        $response = $this->getSimpleResponse(201, json_encode($newUser));
        return $response;
    }

    public function setUserService(UserService $service)
    {
        $this->userService = $service;
        return $this;
    }
}
