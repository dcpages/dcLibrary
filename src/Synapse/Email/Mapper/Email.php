<?php

namespace Synapse\Email\Mapper;

use Synapse\Mapper\AbstractMapper;

/**
 * Email mapper
 */
class Email extends AbstractMapper
{
    /**
     * Use all mapper traits, making this a general purpose mapper
     */
    use InserterTrait;
    use FinderTrait;
    use UpdaterTrait;
    use DeleterTrait;

    /**
     * {@inheritDoc}
     */
    protected $tableName = 'emails';
}
