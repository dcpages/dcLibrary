<?php

namespace Synapse\User\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Synapse\Entity\AbstractEntity;

/**
 * User entity
 */
class User extends AbstractEntity implements UserInterface
{
    protected $roles = [];

    /**
     * {@inheritDoc}
     */
    protected $object = [
        'id'         => null,
        'email'      => null,
        'password'   => null,
        'last_login' => null,
        'created'    => null,
        'enabled'    => null,
    ];

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return $this->object['password'];
    }

    public function setRoles(array $roles = array())
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getUsername()
    {
        return $this->object['email'];
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials()
    {
        // no op
    }
}