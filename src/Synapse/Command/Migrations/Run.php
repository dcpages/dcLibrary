<?php

namespace Synapse\Command\Migrations;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Run extends Command
{
    protected function configure()
    {
        $this->setName('migrations:run')
            ->setDescription('Run all new database migrations');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}
