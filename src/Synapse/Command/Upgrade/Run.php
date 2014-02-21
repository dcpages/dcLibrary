<?php

namespace Synapse\Command\Upgrade;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Db\Adapter\Adapter as DbAdapter;
use SplFileObject;

/**
 * Console command to run the current database upgrade.
 *
 * Runs the upgrade that matches the version of the current codebase, if such an
 * upgrade is actually found and it has not yet been run.
 */
class Run extends Command
{
    /**
     * Database adapter
     *
     * @var Zend\Db\Adapter\Adapter
     */
    protected $db;

    /**
     * Current version of the application
     *
     * @var string
     */
    protected $appVersion;

    /**
     * Set the database adapter
     *
     * @param \Zend\Db\Adapter\Adapter $db
     */
    public function setDatabaseAdapter(DbAdapter $db)
    {
        $this->db = $db;
    }

    /**
     * Set the current app version
     *
     * @param string $version
     */
    public function setAppVersion($version)
    {
        $this->appVersion = $version;
    }

    /**
     * Configure this console command
     */
    protected function configure()
    {
        $this->setName('upgrade:run')
            ->setDescription('Run database upgrade')
            ->addOption(
                'drop-tables',
                null,
                InputOption::VALUE_NONE,
                'If set, all tables will be dropped, and the database rebuilt from the install file.'
            );
    }

    /**
     * Execute this console command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $databaseVersion = $this->databaseVersion();

        if (version_compare($databaseVersion, $this->appVersion, '>')) {
            $message = 'Database version (%s) is newer than codebase (%s). Exiting.';
            $message = sprintf($message, $databaseVersion, $this->appVersion);

            throw new \Exception($message);
        }

        // Run all migrations

        if (! $upgradeFile = $this->currentUpgrade($databaseVersion)) {
            $output->writeln('No upgrade file exists. Exiting.');

            return;
        }

        $class = 'Application\\Upgrades\\'.$upgradeFile->getBasename();

        $upgrade = new $class;

        $upgrade->execute($this->db);
    }

    /**
     * Return an SplFileObject of the current upgrade file, or false if none exists
     *
     * @param  string             $version
     * @return SplFileObject|bool
     */
    protected function currentUpgrade($version)
    {
        $path = APPDIR.'/src/Application/Upgrades/';
        $file = 'Upgrade_'.str_replace('.', '_', $version).'.php';

        if (file_exists($path.$file)) {
            return new SplFileObject($path.$file);
        }

        return false;
    }

    /**
     * Returns the current database version.
     * Assumes that the most recent upgrade is the current database version.
     * (You should always construct and apply upgrades in numerical versioning order.)
     *
     * @return string
     */
    protected function databaseVersion()
    {
        $this->db->query(
            'CREATE TABLE IF NOT EXISTS `app_versions` (
            `version` VARCHAR(50) NOT NULL,
            `timestamp` VARCHAR(14) NOT NULL,
            KEY `timestamp` (`timestamp`)
            )ENGINE=InnoDB DEFAULT CHARSET=utf8',
            DbAdapter::QUERY_MODE_EXECUTE
        );

        $version = $this->db->query(
            'SELECT `version` FROM `app_versions` ORDER BY `timestamp` LIMIT 1',
            DbAdapter::QUERY_MODE_EXECUTE
        )->current();

        return $version;
    }
}
