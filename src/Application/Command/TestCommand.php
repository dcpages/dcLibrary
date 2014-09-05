<?php

namespace Application\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected function configure()
    {
        $this->setName('test:print')
            ->setDescription('Print some text')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'What\'s your name?'
            )
            ->addOption(
                'shout',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will shout in uppercase letters'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $text = 'Hello, ' . $input->getArgument('name');

        if ($input->getOption('shout')) {
            $text = strtoupper($text);
        }

        $output->writeln($text);
    }
}
