<?php

namespace Synapse\Command\Email;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Synapse\Email\Entity\Email;

class Send extends Command
{
    /**
     * Set name, description, arguments, and options for this console command
     */
    protected function configure()
    {
        $this->setName('email:send')
            ->setDescription('Send an email')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'ID of email to send'
            );
    }

    /**
     * Execute this console command to send an email
     *
     * @param  InputInterface  $input  Command line input interface
     * @param  OutputInterface $output Command line output interface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Finding email by ID');

        $emailId = $input->getArgument('id');

        $email = $this->emailMapper->findById($emailId);

        if ($email->isNew()) {
            throw new NotFoundException('Email not found.');
        }

        $output->writeln('Sending email');

        $email = $this->emailSender->send($email);

        if (! $email->getStatus() === Email::STATUS_SENT) {
            $output->writeln('Email did not send successfully.');

            return;
        }

        $output->writeln('Email sent successfully!');
    }
}
