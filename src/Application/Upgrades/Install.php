<?php

namespace Application\Upgrades;

use Synapse\Upgrade\AbstractUpgrade;
use Zend\Db\Adapter\Adapter as DbAdapter;

class Install extends AbstractUpgrade
{
    public function execute(DbAdapter $db)
    {
        // Perform post-database-install chores here
    }
}
