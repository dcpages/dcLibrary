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
        'lastLogin'  => null,
        'created'    => null,
        'enabled'    => null,
    ];
}
