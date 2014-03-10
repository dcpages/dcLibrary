<?php

namespace Synapse\User;

use Synapse\User\Mapper\User as UserMapper;
use Synapse\User\Mapper\UserToken as UserTokenMapper;
use Synapse\User\Entity\User as UserEntity;
use Synapse\User\Entity\UserToken as UserTokenEntity;
use Synapse\View\Email\VerifyRegistration as VerifyRegistrationView;
use Synapse\Email\Entity\Email;

class UserService
{
    protected $userMapper;
    protected $userTokenMapper;

    public function findById($id)
    {
        return $this->userMapper->findById($id);
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
            ->setType(UserTokenEntity::TYPE_REGISTRATION_VERIFICATION)
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

    public function setEmailMapper(EmailMapper $mapper)
    {
        $this->emailMapper = $mapper;
        return $this;
    }

    protected function sendVerificationEmail(UserEntity $user, UserTokenEntity $userToken)
    {
        $view = new VerifyRegistrationView;

        $view->setUserToken($userToken);

        $email = new Email;
        $email = $email->fromArray([
            'recipient_email' => $user->getEmail(),
            'message'         => (string) $view,
        ]);

        $this->emailMapper->persist($email);
    }
}
