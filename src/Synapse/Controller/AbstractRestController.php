<?php

namespace Synapse\Controller;

use Symfony\Component\HttpFoundation\Request;
use Synapse\Rest\Exception\MethodNotImplementedException;

class AbstractRestController
{
    /**
     * Silex hooks into REST controllers here
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function execute(Request $request)
    {
        $method = $request->getMethod();

        if (!method_exists($this, $method)) {
            throw new MethodNotImplementedException(
                sprintf(
                    'HTTP method "%s" has not been implemented in class "%s"',
                    $method,
                    get_class($this)
                )
            );
        }

        return $this->{$method}($request);
    }
}
