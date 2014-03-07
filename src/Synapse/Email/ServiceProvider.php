<?php

namespace Synapse\Email;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Synapse\Email\Entity\Email as EmailEntity;
use Synapse\Email\Mapper\Email as EmailMapper;
use Synapse\Email\MandrillSender;
use Synapse\Stdlib\Arr;

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
            return new EmailMapper($app['email.entity']);
        });

        $app['email.sender'] = $app->share(function (Applictaion $app) {
            $emailConfig = $app['config']->load('email');

            if (! $apiKey = Arr::path($emailConfig, 'sender.mandrill.apiKey')) {
                return;
            }

            return new MandrillSender(
                new Mandrill($apiKey),
                $app['email.mapper']
            );
        });
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
        // noop
    }
}
