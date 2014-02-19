<?php

namespace Synapse\Command\Upgrade;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command
{
    protected function configure()
    {
        $this->setName('upgrade:create')
            ->setDescription('Create a new database upgrade')
            ->addArgument(
                'version',
                InputArgument::REQUIRED,
                'Upgrade version'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}
