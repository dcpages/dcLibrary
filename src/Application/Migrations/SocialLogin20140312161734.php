<?php

namespace Application\Migrations;

use Synapse\Migration\AbstractMigration;
use Zend\Db\Adapter\Adapter as DbAdapter;

/**
 * social login
 */
class SocialLogin20140312161734 extends AbstractMigration
{
    /**
     * Description of this migration, to record in the database when it is run
     *
     * @var string
     */
    protected $description = 'social login';

    /**
     * Timestamp of when this migration was created
     *
     * @var string
     */
    protected $timestamp = '20140312161734';

    /**
     * Run database queries to apply this migration
     *
     * @param  DbAdapter $db
     */
    public function execute(DbAdapter $db)
    {
        $db->query(
            'CREATE TABLE `user_social_logins` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT UNSIGNED NOT NULL,
                `provider` VARCHAR(20) NOT NULL,
                `provider_user_id` VARCHAR(20) NOT NULL,
                `access_token` VARCHAR(40) NOT NULL,
                `access_token_expires` INT NOT NULL,
                `refresh_token` VARCHAR(40) NULL
            )',
            DbAdapter::QUERY_MODE_EXECUTE
        );

        $db->query(
            'ALTER TABLE `users` CHANGE `password` `password` VARCHAR(64) NULL',
            DbAdapter::QUERY_MODE_EXECUTE
        );
    }
}
