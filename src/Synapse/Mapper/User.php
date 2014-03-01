<?php

namespace Synapse\Mapper;

/**
 * User mapper
 */
class User extends AbstractMapper
{
    /**
     * Use all mapper traits, making this a general purpose mapper
     */
    use InserterTrait;
    use FinderTrait;
    use UpdaterTrait;
    use DeleterTrait;

    /**
     * Name of user table
     *
     * @var string
     */
    protected $tableName = 'users';
}
