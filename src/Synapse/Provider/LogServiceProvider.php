<?php

namespace Synapse\Provider;

use Synapse\Stdlib\Arr;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Monolog\Logger;
use Monolog\Handler\LogglyHandler;
use Monolog\Handler\StreamHandler;

class LogServiceProvider implements ServiceProviderInterface
{
    protected $config;

    protected $handlers = [];

    public function register(Application $app)
    {
        $this->config = $app['config']->load('log');

        $this->registerFileHandler($app);
        $this->registerLogglyHandler($app);
        $this->registerRollbarHandler($app);

        $app['logger'] = $app->share(function () use ($app) {
            $handlers = [];

            foreach ($this->handlers as $serviceName) {
                $handlers[] = $app[$serviceName];
            }

            return new Logger('main', $handlers);
        });
    }

    public function boot(Application $app)
    {
        // noop
    }

    protected function registerFileHandler(Application $app)
    {
        $serviceName = 'stream.handler';

        $file = Arr::extract($this->config, ['file.path'])['file']['path'];

        $app[$serviceName] = $app->share(function () use ($app, $file) {
            return new StreamHandler($file, Logger::INFO);
        });

        $this->handlers[] = $serviceName;
    }

    protected function registerLogglyHandler(Application $app)
    {
        $serviceName = 'loggly.handler';

        $token = Arr::extract($this->config, ['loggly.token'])['loggly']['token'];

        if (! $token) {
            return;
        }

        $app[$serviceName] = $app->share(function () use ($app, $token) {
            return new LogglyHandler($token, Logger::INFO);
        });

        $this->handlers[] = $serviceName;
    }

    protected function registerRollbarHandler(Application $app)
    {

    }
}
