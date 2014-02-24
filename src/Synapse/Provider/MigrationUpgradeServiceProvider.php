<?php

namespace Synapse\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Symfony\Component\Console\Application as ConsoleApplication;

use Synapse\Config\Config;
use Synapse\Config\FileReader;

class MigrationUpgradeServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['upgrade.create'] = $app->share(function () use ($app) {
            $command = new \Synapse\Command\Upgrade\Create(
                new \Synapse\View\Upgrade\Create($app['mustache'])
            );

            $command->setDatabaseAdapter($app['db']);

            return $command;
        });

        $app['upgrade.run'] = $app->share(function () use ($app) {
            $command = new \Synapse\Command\Upgrade\Run;

            $command->setDatabaseAdapter($app['db']);

            $command->setAppVersion($app['version']);

            $command->setRunMigrationsCommand($app['migrations.run']);

            return $command;
        });

        $app['migrations.create'] = $app->share(function () use ($app) {
            return new \Synapse\Command\Migrations\Create(
                new \Synapse\View\Migration\Create($app['mustache'])
            );
        });

        $app['migrations.run'] = $app->share(function () use ($app) {
            $command = new \Synapse\Command\Migrations\Run;

            $command->setDatabaseAdapter($app['db']);

            return $command;
        });
    }

    public function boot(Application $app)
    {
        // noop
    }
}
