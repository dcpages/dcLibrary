<?php

namespace Synapse\User;

use Synapse\User\Mapper\User as UserMapper;
use Synapse\User\Mapper\UserToken as UserTokenMapper;
use Synapse\Email\EmailService;
use Synapse\User\Entity\User as UserEntity;
use Synapse\User\Entity\UserToken as UserTokenEntity;
use Synapse\View\Email\VerifyRegistration as VerifyRegistrationView;
use OutOfBoundsException;

/**
 * Service for general purpose tasks regarding the user
 */
class UserService
{
    /**
     * HTTP codes to return for specific exceptions
     */
    const HTTP_CODE_INCORRECT_TOKEN_TYPE = 422;
    const HTTP_CODE_TOKEN_EXPIRED        = 410;
    const HTTP_CODE_TOKEN_NOT_FOUND      = 404;

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
     * Find a user by id
     *
     * @param  int|string $id
     * @return Synapse\User\Entity\User
     */
    public function findById($id)
    {
        return $this->userMapper->findById($id);
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
     * Register a user
     *
     * @param  array                    $userData Data with which to populate the user
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

        $userToken = new UserTokenEntity;
        $userToken->setCreated(time())
            ->setExpires(strtotime('+1 day', time()))
            ->setType(UserTokenEntity::TYPE_VERIFY_REGISTRATION)
            ->setUserId($user->getId())
            ->setToken($userToken->generateToken());

        $userToken = $this->userTokenMapper->persist($userToken);

        $this->sendVerificationEmail($user, $userToken);

        return $user;
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
            throw new OutOfBoundsException('Token not found.', self::HTTP_CODE_TOKEN_NOT_FOUND);
        }

        if ($token->getType() !== UserTokenEntity::TYPE_VERIFY_REGISTRATION) {
            $format  = 'Token specified if of type %s. Expected %s.';
            $message = sprintf($format, $token->getType(), UserTokenEntity::TYPE_VERIFY_REGISTRATION);

            throw new OutOfBoundsException($message, self::HTTP_CODE_INCORRECT_TOKEN_TYPE);
        }

        if ($token->getExpires() < time()) {
            throw new OutOfBoundsException('Token expired', self::HTTP_CODE_TOKEN_EXPIRED);
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
     * Send the verify registration email
     *
     * @param  Synapse\User\Entity\User      $user
     * @param  Synapse\User\Entity\UserToken $userToken
     * @return Synapse\Email\Entity\Email
     */
    protected function sendVerificationEmail(UserEntity $user, UserTokenEntity $userToken)
    {
        $view = $this->verifyRegistrationView;

        $view->setUserToken($userToken);

        $email = $this->emailService->createFromArray([
            'recipient_email' => $user->getEmail(),
            'message'         => (string) $view,
            'subject'         => 'Verify Your Account',
        ]);

        return $email;
    }
}
