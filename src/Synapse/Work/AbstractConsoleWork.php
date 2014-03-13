<?php

namespace Synapse\Work;

use Synapse\Application;
use Synapse\AppliationInitializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * An abstract class for wrapping console commands as Work to be performed by workers
 *
 * Simply extend this class and overload getConsoleCommand() to return the correct service from $app
 */
abstract class AbstractConsoleWork
{
    /**
     * Manually load, configure, and run the console command
     *
     * Inject $this->args as Input object arguments
     */
    public function perform()
    {
        $app = $this->application();

        $command = $this->getConsoleCommand($app);

        $input  = $this->createInput();
        $output = new ConsoleOutput;

        $command->configure();
        $command->execute($input, $output);
    }

    /**
     * Return the Silex application loaded with all routes and services
     *
     * @return Application
     */
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

    /**
     * Create Console Input object with $this->args loaded as Input arguments
     *
     * @return Input
     */
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
