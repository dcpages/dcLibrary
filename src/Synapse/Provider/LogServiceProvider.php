<?php

namespace Synapse\Provider;

use Synapse\Stdlib\Arr;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Monolog\Logger;
use Monolog\Handler\LogglyHandler;
use Monolog\Handler\StreamHandler;
use Synapse\Log\Formatter\ExceptionLineFormatter;
use Synapse\Config\Exception as ConfigException;

/**
 * Service provider for logging services.
 *
 * Register application logger and injected log handlers.
 */
class LogServiceProvider implements ServiceProviderInterface
{
    /**
     * Log configuration
     *
     * @var array
     */
    protected $config;

    /**
     * Handlers to inject into logger. Different handlers will collect here depending on the environment.
     *
     * @var array
     */
    protected $handlers = [];

    /**
     * Register logging related services
     *
     * @param  Application $app Silex application
     */
    public function register(Application $app)
    {
        $this->config = $app['config']->load('log');

        $this->registerFileHandler($app);
        $this->registerLogglyHandler($app);
        $this->registerRollbarHandler($app);

        $app['log'] = $app->share(function () use ($app) {
            $handlers = [];

            foreach ($this->handlers as $serviceName) {
                $handlers[] = $app[$serviceName];
            }

            return new Logger('main', $handlers);
        });
    }

    /**
     * Perform extra chores on boot (none needed here)
     *
     * @param  Application $app
     */
    public function boot(Application $app)
    {
        // noop
    }

    /**
     * Register log handler for files
     *
     * @param  Application $app Silex application
     */
    protected function registerFileHandler(Application $app)
    {
        $serviceName = 'stream.handler';
        $format      = '[%datetime%] %channel%.%level_name%: %message% %context.stacktrace%%extra%'.PHP_EOL;

        $file = Arr::extract($this->config, ['file.path'])['file']['path'];

        $app[$serviceName] = $app->share(function () use ($app, $file, $format) {
            $handler = new StreamHandler($file, Logger::INFO);

            $handler->setFormatter(new ExceptionLineFormatter($format));

            return $handler;
        });

        $this->handlers[] = $serviceName;
    }

    /**
     * Register log handler for Loggly
     *
     * @param  Application $app Silex application
     */
    protected function registerLogglyHandler(Application $app)
    {
        $enableLoggly = Arr::extract($this->config, ['loggly.enable'])['loggly']['enable'];

        if (! $enableLoggly) {
            return;
        }

        if ($app['environment'] === 'development') {
            return;
        }

        $serviceName = 'loggly.handler';

        $token = Arr::extract($this->config, ['loggly.token'])['loggly']['token'];

        if (! $token) {
            throw new ConfigException('Loggly is enabled but the token is not set.');
        }

        $app[$serviceName] = $app->share(function () use ($app, $token) {
            return new LogglyHandler($token, Logger::INFO);
        });

        $this->handlers[] = $serviceName;
    }

    /**
     * Register log handler for Rollbar
     *
     * @param  Application $app Silex application
     */
    protected function registerRollbarHandler(Application $app)
    {

    }
}
