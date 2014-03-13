<?php

namespace Synapse\Work\Email;

use Synapse\Application;
use Synapes\Work\AbstractConsoleWork;

/**
 * Work for sending emails
 */
class Send extends AbstractConsoleWork
{
    /**
     * {@inheritDoc}
     */
    protected function createConsoleCommand(Application $app)
    {
        return $app['email.send'];
    }
}
