<?php

namespace Synapse\User\Controller;

use Symfony\Component\HttpFoundation\Request;
use Synapse\Controller\AbstractRestController;
use Synapse\User\UserService;
use Synapse\User\Entity\UserToken;
use Synapse\User\Entity\User;
use Synapse\Stdlib\Arr;
use OutOfBoundsException;

/**
 * Controller for resetting passwords
 */
class ResetPasswordController extends AbstractRestController
{
    /**
     * @var Synapse\User\UserService
     */
    protected $userService;

    /**
     * Sending reset password email
     *
     * @param  Request $request
     * @return array
     */
    public function post(Request $request)
    {
        $id = $this->loggedInUserId();

        if (! $id) {
            return $this->getSimpleResponse(401, 'Not logged in');
        }

        $user = $this->userService->sendResetPasswordEmail($user);

        return $this->userArrayWithoutPassword($user);
    }

    /**
     * Reset password using token and new password
     *
     * @param  Request $request
     * @return array
     */
    public function put(Request $request)
    {
        $id    = $this->loggedInUserId();
        $token = Arr::get($this->content, 'token');

        $conditions = [
            'user_id' => $id,
            'token'   => $token,
            'type'    => UserToken::TYPE_RESET_PASSWORD,
        ];

        $token = $this->userService->findTokenBy($conditions);

        if (! $token) {
            return $this->getSimpleResponse(404, 'Token not found');
        }

        $password       = Arr::get($this->content, 'password');
        $passwordVerify = Arr::get($this->content, 'password_verify');

        if (! $password) {
            return $this->getSimpleResponse(422, 'Missing required field');
        }

        if ($password !== $passwordVerify) {
            return $this->getSimpleResponse(422, 'Passwords do not match');
        }

        $user = $this->userService->findById($id);
        $user = $this->userService->resetPassword($user, $password);

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

    protected function loggedInUserId()
    {
        return 1;
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
