<?php

namespace Synapse\Command\Upgrade;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Run extends Command
{
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}
