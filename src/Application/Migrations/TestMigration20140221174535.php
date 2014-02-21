<?php

namespace Application\Migrations;

use Synapse\Migration\AbstractMigration;
use Zend\Db\Adapter\Adapter as DbAdapter;

/**
 * Test Migration
 */
class TestMigration20140221174535 extends AbstractMigration
{
    /**
     * Run database queries to apply this migration
     */
    public function execute(DbAdapter $db)
    {
        $db->query(
            'CREATE TABLE `test_table` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(50) NOT NULL,
            )ENGINE=InnoDB'
        );
    }
}
