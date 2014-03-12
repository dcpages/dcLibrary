<?php

namespace Synapse\User\Controller;

use Symfony\Component\HttpFoundation\Request;
use Synapse\Controller\AbstractRestController;
use Synapse\User\Entity\User;
use Synapse\User\UserService;
use Synapse\Application\SecurityAwareInterface;
use Synapse\Application\SecurityAwareTrait;

class UserController extends AbstractRestController implements SecurityAwareInterface
{
    use SecurityAwareTrait;

    protected $userService;

    public function get(Request $request)
    {
        $id = $request->attributes->get('id');

        $user = $this->userService
            ->findById($id)
            ->getArrayCopy();

        unset($user['password']);

        return $user;
    }

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
