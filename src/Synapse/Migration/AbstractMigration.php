<?php

namespace Synapse\Migration;

/**
 * Abstract migration class to be extended by all migrations
 */
abstract class AbstractMigration
{
    /**
     * Database connection
     * @var \Zend\Db\Adapter\Adapter
     */
    protected $db;

    /**
     * Construct the migration
     * @param \Zend\Db\Adapter\Adapter $db Database adapter
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    abstract public function execute();
}
