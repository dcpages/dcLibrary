<?php

namespace Application\Migrations;

use Synapse\Migration\AbstractMigration;
use Zend\Db\Adapter\Adapter as DbAdapter;

/**
 * Create user_token_types table
 */
class CreateUsertokentypesTable20140507193727 extends AbstractMigration
{
    /**
     * Description of this migration, to record in the database when it is run
     *
     * @var string
     */
    protected $description = 'Create user_token_types table';

    /**
     * Timestamp of when this migration was created
     *
     * @var string
     */
    protected $timestamp = '20140507193727';

    /**
     * Run database queries to apply this migration
     *
     * @param  DbAdapter $db
     */
    public function execute(DbAdapter $db)
    {
        $db->query(
            'CREATE TABLE `user_token_types` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `key` VARCHAR(40) NOT NULL,
                PRIMARY KEY (`id`),
                KEY `k_user_token_types_key` (`key`)
            )',
            DbAdapter::QUERY_MODE_EXECUTE
        );

        $db->query(
            'ALTER TABLE `user_tokens` DROP `type`',
            DbAdapter::QUERY_MODE_EXECUTE
        );

        $db->query(
            'ALTER TABLE `user_tokens` ADD `token_type_id` INT UNSIGNED NOT NULL',
            DbAdapter::QUERY_MODE_EXECUTE
        );

        $db->query(
            'ALTER TABLE `user_tokens`
                ADD CONSTRAINT `fk_user_tokens_user_token_type_id`
                    FOREIGN KEY (`token_type_id`)
                    REFERENCES `user_token_types` (`id`),
                ADD KEY `k_user_tokens_user_id_token_type_id` (`user_id`, `token_type_id`)',
            DbAdapter::QUERY_MODE_EXECUTE
        );

        $connection = $db->getDriver()->getConnection();

        $connection->beginTransaction();

        $db->createStatement(
            'INSERT INTO `user_token_types` (`id`, `key`) VALUES
                (1, "verify_registration"),
                (2, "reset_password")'
        )->execute();

        try {
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }
}
