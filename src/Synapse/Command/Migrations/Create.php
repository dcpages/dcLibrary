<?php

namespace Synapse\Command\Migrations;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command
{
    protected $newMigrationView;

    public function __construct($newMigrationView)
    {
        $this->newMigrationView = $newMigrationView;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('migrations:create')
            ->setDescription('Create a new database migration')
            ->addArgument(
                'description',
                InputArgument::REQUIRED,
                'Enter a short description of the migration: '
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $description = $input->getArgument('description');
        $time        = date('YmdHis');
        $classname   = $this->classname($time, $description);
        $filepath    = APPDIR.'/src/Application/Migrations/'.$classname.'.php';

        if (! is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0775, true);
        }

        $view = $this->newMigrationView;

        $view->description($description);
        $view->classname($classname);

        file_put_contents($filepath, (string) $view);
    }

    protected function classname($time, $description)
    {
        $description = substr(strtolower($description), 0, 30);
        $description = ucwords($description);
        $description = preg_replace('/[^a-zA-Z]+/', '', $description);
        return $description.$time;
    }
}
