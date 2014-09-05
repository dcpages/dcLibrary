<?php

namespace Application\Install;

use Synapse\Install\AbstractInstall;
use Zend\Db\Adapter\Adapter as DbAdapter;

class Install extends AbstractInstall
{
    public function execute(DbAdapter $db)
    {
        // Perform post-database-install chores here
        $this->addLocalOAuthClient($db);
    }

    public function addLocalOAuthClient($db)
    {
        $connection = $db->getDriver()->getConnection();

        $connection->beginTransaction();

        $db->createStatement(
            'INSERT INTO oauth_clients (client_id) VALUES (123);'
        )->execute();

        try {
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }
}
