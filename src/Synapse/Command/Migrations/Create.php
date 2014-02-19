<?php

namespace Synapse\Command\Migrations;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command
{
    protected function configure()
    {
        $this->setName('migrations:create')
            ->setDescription('Create a new database migration')
            ->addArgument(
                'description',
                InputArgument::REQUIRED,
                'Enter a short description of the migration: '
            )
            ->addOption(
                'group',
                InputOption::VALUE_REQUIRED,
                'The migration group in which to create this migration. (Defaults to \'default\')',
                'default'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $time  = date('YmdHis');
        $group = $input->getOption('group');
        $class = $this->classname($group, $time);
        $file  = $this->filename($time);
    }

    protected function classname($group, $time)
    {
        $class = ucwords(str_replace('/', ' ', $group));

        $class .= '_'.$time;

        return 'Migration_'.preg_replace('~[^a-zA-Z0-9]+~', '_', $class);
    }
}
