<?php

namespace Synapse\Provider;

use Synapse\Stdlib\Arr;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Monolog\Logger;
use Monolog\Handler\LogglyHandler;
use Synapse\Log\Handler\RollbarHandler;
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
     * Register logging related services
     *
     * @param  Application $app Silex application
     */
    public function register(Application $app)
    {
        $this->config = $app['config']->load('log');

        $handlers = [];

        // File Handler
        $file = Arr::extract($this->config, ['file.path'])['file']['path'];

        if ($file) {
            $handlers[] = $this->fileHandler($file);
        }

        // Loggly Handler
        $enableLoggly = Arr::extract($this->config, ['loggly.enable'])['loggly']['enable'];

        if ($enableLoggly) {
            $handlers[] = $this->logglyHandler();
        }

        // Rollbar Handler
        $enableRollbar = Arr::extract($this->config, ['rollbar.enable'])['rollbar']['enable'];

        if ($enableRollbar) {
            $handlers[] = $this->rollbarHandler($app['environment']);
        }

        $app['log'] = $app->share(function () use ($app, $handlers) {
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
     * Log handler for files
     *
     * @param  string      $file Path of log file
     * @return FileHandler
     */
    protected function fileHandler($file)
    {
        $format      = '[%datetime%] %channel%.%level_name%: %message% %context.stacktrace%%extra%'.PHP_EOL;

        $handler = new StreamHandler($file, Logger::INFO);
        $handler->setFormatter(new ExceptionLineFormatter($format));

        return $handler;
    }

    /**
     * Log handler for Loggly
     *
     * @return LogglyHandler
     */
    protected function logglyHandler()
    {
        $token = Arr::extract($this->config, ['loggly.token'])['loggly']['token'];

        if (! $token) {
            throw new ConfigException('Loggly is enabled but the token is not set.');
        }

        return new LogglyHandler($token, Logger::INFO);
    }

    /**
     * Register log handler for Rollbar
     *
     * @return RollbarHandler
     */
    protected function rollbarHandler($environment)
    {
        $rollbarConfig = Arr::get($this->config, 'rollbar', []);

        $token = Arr::get($rollbarConfig, 'post_server_item_access_token');

        if (! $token) {
            throw new ConfigException('Rollbar is enabled but the post server item access token is not set.');
        }

        return new RollbarHandler(
            $token,
            $environment,
            Arr::get($rollbarConfig, 'root'),
            Logger::ERROR
        );
    }
}
