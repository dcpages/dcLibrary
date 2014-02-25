<?php

namespace Synapse\Command\Upgrade;

use Synapse\View\Upgrade\Create as CreateUpgradeView;
use Synapse\Stdlib\Arr;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CLI command for creating database install files. (DbData, DbStructure.) Based on Kohana Minion task-upgrade.
 */
class Generate extends Command
{
    /**
     * Filename of the database structure install file
     */
    const DATA_STRUCTURE_NAME = 'DbStructure.sql';

    /**
     * Filename of the database data install file
     */
    const DATA_FILE_NAME = 'DbData.sql';

    /**
     * Database config
     *
     * @var array
     */
    protected $dbConfig;

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
     * Inject the root namespace of upgrade classes
     *
     * @param string $upgradeNamespace
     */
    public function setUpgradeNamespace($upgradeNamespace)
    {
        $this->upgradeNamespace = $upgradeNamespace;
    }

    /**
     * Set name, description, arguments, and options for this console command
     */
    protected function configure()
    {
        $this->setName('upgrade:generate')
            ->setDescription('Create database install files for current database version');
    }

    /**
     * Execute this console command, in order to generate install files
     *
     * @param  InputInterface  $input  Command line input interface
     * @param  OutputInterface $output Command line output interface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $outputPath = APPDIR.'/src/'.str_replace('\\', '/', $this->upgradeNamespace);

        $this->dumpStructure($outputPath);
        $output->writeln('  Exported DB structure');

        $this->dumpData($outputPath);
        $output->writeln('  Exported DB data');
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
            escapeshellarg($outputPath.Generate::DATA_STRUCTURE_NAME)
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
        $tables = array_map('escapeshellarg', Arr::get($this->dbConfig, 'data_tables', []));

        $command = sprintf(
            'mysqldump %s %s -u %s -p%s --no-create-info > %s',
            escapeshellarg($this->dbConfig['database']),
            implode(' ', $tables),
            escapeshellarg($this->dbConfig['username']),
            escapeshellarg($this->dbConfig['password']),
            escapeshellarg($outputPath.Generate::DATA_FILE_NAME)
        );

        return shell_exec($command);
    }
}
