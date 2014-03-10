<?php

namespace Synapse\OAuth2\Storage;

use Synapse\User\Mapper\User as UserMapper;

use OAuth2\Storage\Pdo as BasePdoStorage;

class Pdo extends BasePdoStorage
{
    protected $userMapper;

    /**
     * Set the user mapper
     * @param UserMapper $userMapper
     */
    public function setUserMapper(UserMapper $userMapper)
    {
        $this->userMapper = $userMapper;
        return $this;
    }

    /**
     * Get the user as an array
     *
     * @param  string $email the user's email address
     * @return array
     */
    public function getUserDetails($email)
    {
        $user = $this->getUser($email);
        return array(
            'user_id' => $user->getId(),
        );
    }

    /**
     * Get the user to check their credentials
     *
     * @param  string $email the user's email address
     * @return Synapse\Entity\User
     */
    public function getUser($email)
    {
        $user = $this->userMapper->findByEmail($email);

        if (!$user) {
            return false;
        }

        return $user;
    }

    /**
     * Verify the user's password hash
     * @param  Synapse\Entity\User $user     the user to check the given password against
     * @param  string $password the password to check
     * @return boolean whether the password is valid
     */
    public function checkPassword($user, $password)
    {
        return password_verify($password, $user->getPassword());
    }
}
