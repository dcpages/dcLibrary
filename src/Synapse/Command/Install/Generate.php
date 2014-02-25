<?php

namespace Synapse\Command\Install;

use Synapse\Stdlib\Arr;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CLI command for creating database install files. (DbData, DbStructure.) Based on Kohana Minion task-upgrade.
 */
class Generate extends Command
{
    /**
     * Filename of the database structure install file
     */
    const STRUCTURE_FILE = 'DbStructure.sql';

    /**
     * Filename of the database data install file
     */
    const DATA_FILE = 'DbData.sql';

    /**
     * Database config
     *
     * @var array
     */
    protected $dbConfig;

    /**
     * Install config
     *
     * @var array
     */
    protected $installConfig;

    /**
     * Root namespace of upgrade classes
     *
     * @var string
     */
    protected $upgradeNamespace = 'Application\Upgrades\\';

    /**
     * Set database config property
     *
     * @param array $dbConfig
     */
    public function setDbConfig(array $dbConfig)
    {
        $this->dbConfig = $dbConfig;
    }

    /**
     * Set install config property
     *
     * @param array $installConfig
     */
    public function setInstallConfig(array $installConfig)
    {
        $this->installConfig = $installConfig;
    }

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
     * Return the upgrade namespace
     *
     * @return string
     */
    public function getUpgradeNamespace()
    {
        return $this->upgradeNamespace;
    }

    /**
     * Set name, description, arguments, and options for this console command
     */
    protected function configure()
    {
        $this->setName('upgrade:generate')
            ->setDescription('Generate database install files to match the current database');
    }

    /**
     * Execute this console command, in order to generate install files
     *
     * @param  InputInterface  $input  Command line input interface
     * @param  OutputInterface $output Command line output interface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $outputPath = $this->upgradePath();

        $output->write(['', '  Generating install files...', ''], true);

        $this->dumpStructure($outputPath);
        $output->writeln('  Exported DB structure');

        $this->dumpData($outputPath);
        $output->write(['  Exported DB data', ''], true);
    }

    /**
     * Return the path to upgrade files based on the upgrade namespace provided
     *
     * @return string
     */
    public function upgradePath()
    {
        return APPDIR.'/src/'.str_replace('\\', '/', $this->upgradeNamespace);
    }

    /**
     * Export database structure to dbStructure install file
     *
     * @param  string $outputPath Path where file should be exported
     */
    protected function dumpStructure($outputPath)
    {
        $command = sprintf(
            'mysqldump %s -u %s -p%s --no-data | sed "s/AUTO_INCREMENT=[0-9]*//" > %s',
            escapeshellarg($this->dbConfig['database']),
            escapeshellarg($this->dbConfig['username']),
            escapeshellarg($this->dbConfig['password']),
            escapeshellarg($outputPath.Generate::STRUCTURE_FILE)
        );

        return shell_exec($command);
    }

    /**
     * Export database data to dbData install file
     *
     * @param  string $outputPath Path where file should be exported
     */
    protected function dumpData($outputPath)
    {
        $tables = array_map('escapeshellarg', Arr::get($this->installConfig, 'dataTables', []));

        $command = sprintf(
            'mysqldump %s %s -u %s -p%s --no-create-info > %s',
            escapeshellarg($this->dbConfig['database']),
            implode(' ', $tables),
            escapeshellarg($this->dbConfig['username']),
            escapeshellarg($this->dbConfig['password']),
            escapeshellarg($outputPath.Generate::DATA_FILE)
        );

        return shell_exec($command);
    }
}
