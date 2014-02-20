<?php

namespace Synapse\Migration;

use Zend\Db\Adapter\Adapter as DbAdapter;

/**
 * Abstract migration class to be extended by all migrations
 */
abstract class AbstractMigration
{
    /**
     * Execute the migration
     */
    abstract public function execute(DbAdapter $db);
}
