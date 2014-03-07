<?php

namespace Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Synapse\Entity\Email as EmailEntity;
use Synapse\Mapper\Email as EmailMapper;
use Synapse\Email\Sender;
use Synapse\Stdlib\Arr;

class EmailServiceProvider implements ServiceProviderInterface
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

            return new Sender(
                new Mandrill($apiKey)
            );
        });
    }
}
