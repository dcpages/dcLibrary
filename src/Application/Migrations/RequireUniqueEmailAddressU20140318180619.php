<?php

namespace Application\Migrations;

use Synapse\Migration\AbstractMigration;
use Zend\Db\Adapter\Adapter as DbAdapter;

/**
 * Require unique email address upon registration
 */
class RequireUniqueEmailAddressU20140318180619 extends AbstractMigration
{
    /**
     * Description of this migration, to record in the database when it is run
     *
     * @var string
     */
    protected $description = 'Require unique email address upon registration';

    /**
     * Timestamp of when this migration was created
     *
     * @var string
     */
    protected $timestamp = '20140318180619';

    /**
     * Run database queries to apply this migration
     *
     * @param  DbAdapter $db
     */
    public function execute(DbAdapter $db)
    {
        // Simple query example:
        $db->query(
            'ALTER TABLE `users`
            ADD UNIQUE KEY `email` (`email`)',
            DbAdapter::QUERY_MODE_EXECUTE
        );
    }
}
