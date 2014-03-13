<?php

namespace Synapse\Work;

use Synapse\Application;
use Synapse\AppliationInitializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Output\ConsoleOutput;

abstract class AbstractConsoleWork
{
    public function perform()
    {
        $app = $this->application();

        $command = $this->getConsoleCommand($app);

        $input  = $this->createInput();
        $output = new ConsoleOutput;

        $command->configure();
        $command->execute($input, $output);
    }

    protected function application()
    {
        // Initialize the Silex Application
        $applicationInitializer = new ApplicationInitializer;

        $app = $applicationInitializer->initialize();

        // Set the default routes and services
        $defaultRoutes   = new Application\Routes;
        $defaultServices = new Application\Services;

        $defaultRoutes->define($app);
        $defaultServices->register($app);

        // Set the application-specific routes and services
        $appRoutes   = new \Application\Routes;
        $appServices = new \Application\Services;

        $appRoutes->define($app);
        $appServices->register($app);

        return $app;
    }

    protected function createInput()
    {
        $input = new Input;

        foreach ($this->args as $key => $value) {
            $input->setArgument($key, $value);
        }

        return $input;
    }

    /**
     * @param  Application $app
     * @return Command          The console command this job should run
     */
    abstract protected function getConsoleCommand(Application $app)
    {
        // Return the console command object to execute
    }
}
