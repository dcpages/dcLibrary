<?php

namespace SynapseTest\Command\Email;

use PHPUnit_Framework_TestCase;
use Synapse\Command\Email\Send;
use Synapse\Command\Install\Generate;
use Synapse\Email\Mapper\Email as EmailMapper;
use Synapse\Email\SenderInterface;

class EmailTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->sendCommand = new Send();

        // Create mocks
        $this->mockEmailMapper = $this->getMockBuilder('Synapse\Email\Mapper\Email')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockEmailSender = $this->getMock('Synapse\Email\SenderInterface');
        $this->mockInputInterface = $this->getMock('Symfony\Component\Console\Input\InputInterface');
        $this->mockOutputInterface = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
    }

    /**
     * @expectedException LogicException
     */
    public function testThrowsExceptionIfEmailSenderNotSet()
    {
        $this->sendCommand->setEmailMapper($this->mockEmailMapper);

        $this->sendCommand->run(
            $this->mockInputInterface,
            $this->mockOutputInterface
        );
    }

    /**
     * @expectedException LogicException
     */
    public function testThrowsExceptionIfEmailMapperNotSet()
    {
        $this->sendCommand->setEmailSender($this->mockEmailSender);

        $this->sendCommand->run(
            $this->mockInputInterface,
            $this->mockOutputInterface
        );
    }
}
