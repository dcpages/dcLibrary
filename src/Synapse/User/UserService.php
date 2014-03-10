<?php

namespace Synapse\User;

use Synapse\User\Mapper\User as UserMapper;
use Synapse\User\Mapper\UserToken as UserTokenMapper;
use Synapse\Email\EmailService;
use Synapse\User\Entity\User as UserEntity;
use Synapse\User\Entity\UserToken as UserTokenEntity;
use Synapse\View\Email\VerifyRegistration as VerifyRegistrationView;
use OutOfBoundsException;

class UserService
{
    protected $userMapper;
    protected $userTokenMapper;
    protected $emailMapper;
    protected $verifyRegistrationView;

    public function findById($id)
    {
        return $this->userMapper->findById($id);
    }

    public function findTokenBy(array $where)
    {
        return $this->userTokenMapper->findBy($where);
    }

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

    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function verifyRegistration(UserTokenEntity $token)
    {
        if ($token->isNew()) {
            throw new OutOfBoundsException('Token not found.');
        }

        if ($token->getType() !== UserTokenEntity::TYPE_VERIFY_REGISTRATION) {
            $format  = 'Token specified if of type %s. Expected %s.';
            $message = sprintf($format, $token->getType(), UserTokenEntity::TYPE_VERIFY_REGISTRATION);

            throw new OutOfBoundsException($message);
        }

        if ($token->getExpires() < time()) {
            throw new OutOfBoundsException('Token expired');
        }

        $user = $this->findById($token->getUserId());

        if ($user->isNew()) {
            throw new OutOfBoundsException('User not found.');
        }

        if ($user->getVerified()) {
            throw new OutOfBoundsException('User already verified.');
        }

        // Token looks good; verify user and delete the token
        $user->setVerified(true);

        $this->userMapper->persist($user);

        $this->userTokenMapper->delete($token);

        return $user;
    }

    public function setUserMapper(UserMapper $mapper)
    {
        $this->userMapper = $mapper;
        return $this;
    }

    public function setUserTokenMapper(UserTokenMapper $mapper)
    {
        $this->userTokenMapper = $mapper;
        return $this;
    }

    public function setEmailService(EmailService $service)
    {
        $this->emailService = $service;
        return $this;
    }

    public function setVerifyRegistrationView(VerifyRegistrationView $view)
    {
        $this->verifyRegistrationView = $view;
        return $this;
    }

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
