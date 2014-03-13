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
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` INT UNSIGNED NOT NULL,
                `provider` VARCHAR(20) NOT NULL,
                `provider_user_id` VARCHAR(20) NOT NULL,
                `access_token` VARCHAR(40) NOT NULL,
                `access_token_expires` INT NOT NULL,
                `refresh_token` VARCHAR(40) NULL,
                PRIMARY KEY (`id`),
                KEY `k_social_logins_user_id_provider` (`user_id`, `provider`),
                UNIQUE KEY `uk_social_logins_provider_provider_user_id` (`provider`, `provider_user_id`),
                FOREIGN KEY `fk_user_social_logins_user_id` (`user_id`)
                    REFERENCES `users` (`id`)
                    ON UPDATE CASCADE ON DELETE CASCADE
            )',
            DbAdapter::QUERY_MODE_EXECUTE
        );

        $db->query(
            'ALTER TABLE `users` CHANGE `password` `password` VARCHAR(64) NULL',
            DbAdapter::QUERY_MODE_EXECUTE
        );

        $db->query(
            'ALTER TABLE `oauth_access_tokens`
                CHANGE `user_id` `user_id` INT UNSIGNED NOT NULL,
                ADD FOREIGN KEY `fk_oauth_access_tokens_user_id` (`user_id`)
                    REFERENCES `users` (`id`)
                    ON UPDATE CASCADE ON DELETE CASCADE',
            DbAdapter::QUERY_MODE_EXECUTE
        );

        $db->query(
            'ALTER TABLE `oauth_refresh_tokens`
                CHANGE `user_id` `user_id` INT UNSIGNED NOT NULL,
                ADD FOREIGN KEY `fk_oauth_refresh_tokens_user_id` (`user_id`)
                    REFERENCES `users` (`id`)
                    ON UPDATE CASCADE ON DELETE CASCADE',
            DbAdapter::QUERY_MODE_EXECUTE
        );

        $db->query(
            'ALTER TABLE `oauth_authorization_codes`
                CHANGE `user_id` `user_id` INT UNSIGNED NOT NULL,
                ADD FOREIGN KEY `fk_oauth_auth_codes_user_id` (`user_id`)
                    REFERENCES `users` (`id`)
                    ON UPDATE CASCADE ON DELETE CASCADE',
            DbAdapter::QUERY_MODE_EXECUTE
        );
    }
}
