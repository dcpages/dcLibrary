<?php

namespace Synapse\Email;

use Synapse\Entity\Email;
use Mandrill;

/**
 * Service to send emails
 */
class Sender
{
    /**
     * @var Mandrill
     */
    protected $mandrill;

    /**
     * @param Mandrill $mandrill
     */
    public function __construct(Mandrill $mandrill)
    {
        $this->mandrill = $mandrill;
    }

    /**
     * Send an email
     *
     * @param  Email  $email
     * @return mixed         Result of attempt to send email
     */
    public function send(Email $email)
    {
        $message = $this->buildMessage($email);

        return $this->mandrill->messages->send($message);
    }

    /**
     * Build Mandrill compatible message array from email entity
     *
     * @param  Email  $emails
     * @return array
     */
    protected function buildMessage(Email $email)
    {
        return [];
    }
}
