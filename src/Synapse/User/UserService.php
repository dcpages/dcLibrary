<?php

namespace Synapse\User;

use Synapse\User\Mapper\User as UserMapper;
use Synapse\User\Entity\User as UserEntity;

class UserService
{
    protected $userMapper;

    public function findById($id)
    {
        return $this->userMapper->findById($id);
    }

    public function findByEmail($email)
    {
        return $this->userMapper->findByEmail($email);
    }

    public function register(array $userData)
    {
        $userEntity = new UserEntity;
        $userEntity->setEmail($userData['email'])
            ->setPassword($this->hashPassword($userData['password']))
            ->setCreated(time())
            ->setEnabled(true);

        return $this->userMapper->persist($userEntity);
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

    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function setUserMapper(UserMapper $mapper)
    {
        $this->userMapper = $mapper;
        return $this;
    }
}
