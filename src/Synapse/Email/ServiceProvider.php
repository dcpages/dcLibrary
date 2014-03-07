<?php

namespace Synapse\Email;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Synapse\Email\Entity\Email as EmailEntity;
use Synapse\Email\Mapper\Email as EmailMapper;
use Synapse\Email\MandrillSender;
use Synapse\Command\Email\Send as SendEmailCommand;
use Synapse\Stdlib\Arr;
use Mandrill;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        $app['email.entity'] = function () {
            return new EmailEntity;
        };

        $app['email.mapper'] = $app->share(function (Application $app) {
            return new EmailMapper($app['db'], $app['email.entity']);
        });

        $app['email.sender'] = $app->share(function (Application $app) {
            $emailConfig = $app['config']->load('email');

            if (! $apiKey = Arr::path($emailConfig, 'sender.mandrill.apiKey')) {
                return;
            }

            return new MandrillSender(
                new Mandrill($apiKey),
                $app['email.mapper']
            );
        });

        $app['email.send'] = $app->share(function (Application $app) {
            $command = new SendEmailCommand;

            $command->setEmailMapper($app['email.mapper']);
            $command->setEmailSender($app['email.sender']);

            return $command;
        });

        $app->command('email.send');
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
        // noop
    }
}
