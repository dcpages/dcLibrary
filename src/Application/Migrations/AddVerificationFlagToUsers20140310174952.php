<?php

namespace Application\Migrations;

use Synapse\Migration\AbstractMigration;
use Zend\Db\Adapter\Adapter as DbAdapter;

/**
 * Add verification flag to users table
 */
class AddVerificationFlagToUsers20140310174952 extends AbstractMigration
{
    /**
     * Description of this migration, to record in the database when it is run
     *
     * @var string
     */
    protected $description = 'Add verification flag to users table';

    /**
     * Timestamp of when this migration was created
     *
     * @var string
     */
    protected $timestamp = '20140310174952';

    /**
     * Run database queries to apply this migration
     *
     * @param  DbAdapter $db
     */
    public function execute(DbAdapter $db)
    {
        // Simple query example:
        $db->query(
            'ALTER TABLE `users` ADD `verified` TINYINT(4) DEFAULT NULL AFTER `enabled`',
            DbAdapter::QUERY_MODE_EXECUTE
        );
    }
}
