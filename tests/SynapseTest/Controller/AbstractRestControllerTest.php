<?php

namespace SynapseTest\Controller;

use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

class AbstractRestControllerTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->controller = new RestController;
    }

    /**
     * @expectedException Synapse\Rest\Exception\MethodNotImplementedException
     */
    public function testExecuteThrowsExceptionIfMethodNotImplemented()
    {
        $request = new Request;
        $request->setMethod('POST');

        $this->controller->execute($request);
    }

    public function testGetReturnsResponse()
    {
        $request = new Request;
        $request->setMethod('GET');

        $response = $this->controller->execute($request);

        $this->assertInstanceOf('Symfony\\Component\\HttpFoundation\\Response', $response);
        $this->assertEquals('test', (string) $response->getContent());
    }
}
