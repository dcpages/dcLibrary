<?php

namespace Synapse\User\Controller;

use Symfony\Component\HttpFoundation\Request;
use Synapse\Controller\AbstractRestController;
use Synapse\User\Entity\User;
use Synapse\User\UserService;
use Synapse\Application\SecurityAwareInterface;
use Synapse\Application\SecurityAwareTrait;
use Synapse\Stdlib\Arr;

class UserController extends AbstractRestController implements SecurityAwareInterface
{
    use SecurityAwareTrait;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * Return a user entity
     *
     * @param  Request $request
     * @return array
     */
    public function get(Request $request)
    {
        $id = $request->attributes->get('id');

        $user = $this->userService
            ->findById($id);

        return $this->userArrayWithoutPassword($user);
    }

    /**
     * Create a user
     *
     * @param  Request $request
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function post(Request $request)
    {
        $user = $this->content;

        if (! isset($user['email'], $user['password'])) {
            return $this->getSimpleResponse(422, 'Missing required field');
        }

        $newUser = $this->userService->register($user);
        $newUser = $this->userArrayWithoutPassword($newUser);

        $newUser['_href'] = $this->url('user-entity', array('id' => $newUser['id']));

        $response = $this->getSimpleResponse(201, json_encode($newUser));
        return $response;
    }

    /**
     * Edit a user; requires the user to be logged in and the current password provided
     *
     * @param  Request $request
     * @return array
     */
    public function put(Request $request)
    {
        $user = $this->user();

        // Ensure the user in question is logged in
        if ($request->attributes->get('id') !== $user->getId()) {
            return $this->getSimpleResponse(403, 'Access denied');
        }

        $changes = $this->content;

        $verifyCurrentPassword = (isset($changes['email']) or isset($changes['password']));

        if ($verifyCurrentPassword) {
            $currentPassword = Arr::get($this->content, 'current_password');

            if (! password_verify($currentPassword, $user->getPassword())) {
                return $this->getSimpleResponse(403, 'Current password missing or incorrect');
            }
        }

        $update = [];

        // Update email
        if (isset($changes['email'])) {
            $update['email'] = $changes['email'];
        }

        // Update password
        if (isset($changes['password'])) {
            $password        = Arr::get($this->content, 'password');

            if (! $password) {
                return $this->getSimpleResponse(422, 'Password cannot be empty');
            }

            $update['password'] = $this->userService->hashPassword($changes['password']);
        }

        if (! $update) {
            return $this->userArrayWithoutPassword($user);
        }

        $user = $user->exchangeArray($update);
        $user = $this->userService->update($user);

        return $this->userArrayWithoutPassword($user);
    }

    /**
     * @param UserService $service
     */
    public function setUserService(UserService $service)
    {
        $this->userService = $service;
        return $this;
    }

    /**
     * Transform the User entity into an array and remove the password element
     *
     * @param  User   $user
     * @return array
     */
    protected function userArrayWithoutPassword(User $user)
    {
        $user = $user->getArrayCopy();

        unset($user['password']);

        return $user;
    }
}
