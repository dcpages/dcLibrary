<?php

namespace Synapse\Upgrade;

/**
 * Abstract upgrade class to be extended by all upgrades
 */
abstract class AbstractUpgrade
{
    /**
     * Database connection
     * @var \Zend\Db\Adapter\Adapter
     */
    protected $db;

    /**
     * Construct the upgrade
     * @param \Zend\Db\Adapter\Adapter $db Database adapter
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Execute the upgrade
     */
    abstract public function execute();
}
