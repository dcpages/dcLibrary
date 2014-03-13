<?php

namespace Synapse\User;

use Synapse\User\Mapper\User as UserMapper;
use Synapse\User\Mapper\UserToken as UserTokenMapper;
use Synapse\Email\EmailService;
use Synapse\User\Entity\User as UserEntity;
use Synapse\User\Entity\UserToken as UserTokenEntity;
use Synapse\View\Email\VerifyRegistration as VerifyRegistrationView;
use Synapse\View\Email\ResetPassword as ResetPasswordView;
use OutOfBoundsException;

/**
 * Service for general purpose tasks regarding the user
 */
class UserService
{
    /**
     * Error codes to return for specific exceptions
     */
    const INCORRECT_TOKEN_TYPE = 1;
    const TOKEN_EXPIRED        = 2;
    const TOKEN_NOT_FOUND      = 3;

    /**
     * @var Synapse\User\Mapper\User
     */
    protected $userMapper;

    /**
     * @var Synapse\User\Mapper\UserToken
     */
    protected $userTokenMapper;

    /**
     * @var Synapse\Email\EmailService
     */
    protected $emailService;

    /**
     * @var Synapse\View\Email\VerifyRegistration
     */
    protected $verifyRegistrationView;

    /**
     * @var Synapse\View\Email\ResetPassword
     */
    protected $resetPasswordView;

    /**
     * Find a user by id
     *
     * @param  int|string $id
     * @return Synapse\User\Entity\User
     */
    public function findById($id)
    {
        return $this->userMapper->findById($id);
    }

    public function findByEmail($email)
    {
        return $this->userMapper->findByEmail($email);
    }

    /**
     * Find token by given conditions
     *
     * Conditions should be provided in the following format:
     *
     *   [$field => $value, $field2 => $value2]
     *
     *   Translates to: WHERE $field = $value AND $field2 = $value2
     *
     * @param  array  $where Array of conditions to pass to the mapper
     * @return Zend\Db\ResultSet\AbstractResultSet|bool
     */
    public function findTokenBy(array $where)
    {
        return $this->userTokenMapper->findBy($where);
    }

    /**
     * Delete a user token
     *
     * @param  UserToken $token
     * @return Zend\Db\ResultSet\ResultSet
     */
    public function deleteToken(UserTokenEntity $token)
    {
        return $this->userTokenMapper->delete($token);
    }

    /**
     * Register a user
     *
     * @param  array $userData Data with which to populate the user
     * @return Synapse\User\Entity\User
     */
    public function register(array $userData)
    {
        $userEntity = new UserEntity;
        $userEntity->setEmail($userData['email'])
            ->setPassword($this->hashPassword($userData['password']))
            ->setCreated(time())
            ->setEnabled(true)
            ->setVerified(false);

        $user = $this->userMapper->persist($userEntity);

        $userToken = $this->createUserToken([
            'type'    => UserTokenEntity::TYPE_VERIFY_REGISTRATION,
            'user_id' => $user->getId(),
        ]);

        // Create the verify registration email
        $this->verifyRegistrationView->setUserToken($userToken);

        $email = $this->emailService->createFromArray([
            'recipient_email' => $user->getEmail(),
            'subject'         => 'Verify Your Account',
            'message'         => (string) $this->verifyRegistrationView,
        ]);

        return $user;
    }

    public function registerWithoutPassword(array $userData)
    {
        $userEntity = new UserEntity;
        $userEntity->setEmail($userData['email'])
            ->setPassword(null)
            ->setCreated(time())
            ->setEnabled(true);

        return $this->userMapper->persist($userEntity);
    }

    /**
     * Reset a user's password
     *
     * @param  Synapse\User\EntityUserEntity $user
     * @return Synapse\User\EntityUserEntity
     */
    public function sendResetPasswordEmail(UserEntity $user)
    {
        $userToken = $this->createUserToken([
            'type'    => UserTokenEntity::TYPE_RESET_PASSWORD,
            'user_id' => $user->getId(),
        ]);

        $this->resetPasswordView->setUserToken($userToken);

        $email = $this->emailService->createFromArray([
            'recipient_email' => $user->getEmail(),
            'subject'         => 'Reset Your Password',
            'message'         => (string) $this->resetPasswordView,
        ]);

        return $user;
    }

    /**
     * Change the password
     *
     * @param  UserEntity $user        The user whose password to change
     * @param  string     $newPassword The new password
     * @return UserEntity
     */
    public function resetPassword(UserEntity $user, $newPassword)
    {
        $hashedPassword = $this->hashPassword($newPassword);

        $user->setPassword($hashedPassword);

        return $this->userMapper->persist($user);
    }

    /**
     * Hash a password using bcrypt
     *
     * @param  string $password
     * @return string
     */
    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Verify the user given a verify registration token
     *
     * @param  Synapse\User\Entity\UserToken $token
     * @return Synapse\User\Entity\User
     * @throws OutOfBoundsException
     */
    public function verifyRegistration(UserTokenEntity $token)
    {
        if ($token->isNew()) {
            throw new OutOfBoundsException('Token not found.', self::TOKEN_NOT_FOUND);
        }

        if ($token->getType() !== UserTokenEntity::TYPE_VERIFY_REGISTRATION) {
            $format  = 'Token specified if of type %s. Expected %s.';
            $message = sprintf($format, $token->getType(), UserTokenEntity::TYPE_VERIFY_REGISTRATION);

            throw new OutOfBoundsException($message, self::INCORRECT_TOKEN_TYPE);
        }

        if ($token->getExpires() < time()) {
            throw new OutOfBoundsException('Token expired', self::TOKEN_EXPIRED);
        }

        $user = $this->findById($token->getUserId());

        // Token looks good; verify user and delete the token
        $user->setVerified(true);

        $this->userMapper->persist($user);

        $this->userTokenMapper->delete($token);

        return $user;
    }

    /**
     * @param Synapse\User\Mapper\User $mapper
     */
    public function setUserMapper(UserMapper $mapper)
    {
        $this->userMapper = $mapper;
        return $this;
    }

    /**
     * @param Synapse\User\Mapper\UserToken $mapper
     */
    public function setUserTokenMapper(UserTokenMapper $mapper)
    {
        $this->userTokenMapper = $mapper;
        return $this;
    }

    /**
     * @param Synapse\Email\EmailService $service
     */
    public function setEmailService(EmailService $service)
    {
        $this->emailService = $service;
        return $this;
    }

    /**
     * @param Synapse\View\Email\VerifyRegistration $view
     */
    public function setVerifyRegistrationView(VerifyRegistrationView $view)
    {
        $this->verifyRegistrationView = $view;
        return $this;
    }

    /**
     * @param Synapse\View\Email\ResetPassword $view
     */
    public function setResetPasswordView(ResetPasswordView $view)
    {
        $this->resetPasswordView = $view;
        return $this;
    }

    /**
     * Create a user token and persist it in the database
     *
     * @param  array  $data Data to populate the user token
     * @return Synapse\User\Entity\UserToken
     */
    protected function createUserToken(array $data)
    {
        $userToken = new UserTokenEntity;

        $defaults = [
            'created' => time(),
            'expires' => strtotime('+1 day', time()),
            'token'   => $userToken->generateToken(),
        ];

        $userToken = $userToken->exchangeArray(array_merge($defaults, $data));

        return $this->userTokenMapper->persist($userToken);
    }
}
