<?php

namespace SynapseTest\Config;

use PHPUnit_Framework_TestCase;
use Synapse\Config;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->config = new Config\Config;
    }

    public function testAttachReader()
    {
        $this->config->attach(new Config\FileReader(__DIR__.'/data'));
        $this->assertEquals(1, count($this->config->getReaders()));
    }
}
