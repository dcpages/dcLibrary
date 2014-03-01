<?php

namespace Synapse\Entity;

/**
 * User entity
 */
class User extends AbstractEntity
{
    /**
     * Entity data
     *
     * @var array
     */
    protected $object = [
        'id'         => null,
        'email'      => null,
        'password'   => null,
        'last_login' => null,
        'created'    => null,
        'enabled'    => null,
    ];
}
