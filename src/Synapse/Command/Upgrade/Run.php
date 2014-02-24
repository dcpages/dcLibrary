<?php

namespace Synapse\Command\Upgrade;

use Symfony\Component\Console\Command\Command;
use Synapse\Command\Upgrade\AbstractUpgradeCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Db\Adapter\Adapter as DbAdapter;
use SplFileObject;

/**
 * Console command to run the current database upgrade. Based on Kohana Minion task-migrations.
 *
 * Runs the upgrade that matches the version of the current codebase, if such an
 * upgrade is actually found and it has not yet been run.
 *
 * Uses the app_versions table to record which version upgrades have been run.
 */
class Run extends AbstractUpgradeCommand
{
    /**
     * Root namespace of upgrade classes
     *
     * @var string
     */
    protected $upgradeNamespace = 'Application\Upgrades\\';

    /**
     * Current version of the application
     *
     * @var string
     */
    protected $appVersion;

    /**
     * Run migrations console command object
     *
     * @var Symfony\Component\Console\Command\Command
     */
    protected $runMigrationsCommand;

    /**
     * Inject the root namespace of upgrade classes
     *
     * @param string $upgradeNamespace
     */
    public function setUpgradeNamespace($upgradeNamespace)
    {
        $this->upgradeNamespace = $upgradeNamespace;
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
     * Set the run migrations console command
     *
     * @param Symfony\Component\Console\Command\Command
     */
    public function setRunMigrationsCommand(Command $command)
    {
        $this->runMigrationsCommand = $command;
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
        // Console message heading padded by a newline
        $output->write(['', '  -- APP UPGRADE --', '  Executing new migrations before upgrading'], true);

        // Run all migrations
        $this->runMigrationsCommand->execute($input, $output);

        $this->createAppVersionsTable();

        $databaseVersion = $this->currentDatabaseVersion();

        if (version_compare($databaseVersion, $this->appVersion, '>')) {
            $message = 'Database version (%s) is newer than codebase (%s). Exiting.';
            $message = sprintf($message, $databaseVersion, $this->appVersion);

            throw new \Exception($message);
        }

        if ($databaseVersion === $this->appVersion) {
            $output->write(['  The database is up-to-date. Exiting.', ''], true);
            return;
        }

        $upgradeFile = $this->currentUpgrade($this->appVersion);

        if ($upgradeFile === false) {
            $message = sprintf('  No upgrade file exists for current app version %s. Exiting.', $this->appVersion);

            $output->write([$message, ''], true);

            return;
        }

        $class = $this->upgradeNamespace.$upgradeFile->getBasename('.php');

        $upgrade = new $class;

        $output->writeln(sprintf('  Upgrading to version %s...', $this->appVersion));

        $upgrade->execute($this->db);

        $this->recordUpgrade($this->appVersion);

        $output->write([sprintf('  Done!', $this->appVersion), ''], true);
    }

    /**
     * Return an SplFileObject of the current upgrade file, or false if none exists
     *
     * @param  string             $version
     * @return SplFileObject|bool
     */
    protected function currentUpgrade($version)
    {
        $path = APPDIR.'/src/'.str_replace('\\', '/', $this->upgradeNamespace);
        $file = 'Upgrade_'.str_replace('.', '_', $version).'.php';

        if (file_exists($path.$file)) {
            $file = new SplFileObject($path.$file);

            require $file->getPathname();

            return $file;
        }

        return false;
    }

    /**
     * Create app_versions table if not exists
     */
    protected function createAppVersionsTable()
    {
        $this->db->query(
            'CREATE TABLE IF NOT EXISTS `app_versions` (
            `version` VARCHAR(50) NOT NULL,
            `timestamp` VARCHAR(14) NOT NULL,
            KEY `timestamp` (`timestamp`))',
            DbAdapter::QUERY_MODE_EXECUTE
        );
    }

    /**
     * Records the upgrade in the app_versions table.
     *
     * @param  string $version
     */
    protected function recordUpgrade($version)
    {
        $query = 'INSERT INTO `app_versions` (`version`, `timestamp`) VALUES ("%s", "%s")';
        $query = sprintf($query, $version, time());

        $this->db->query($query, DbAdapter::QUERY_MODE_EXECUTE);
    }
}
