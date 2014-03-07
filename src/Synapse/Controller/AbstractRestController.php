<?php

namespace Synapse\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Synapse\Rest\Exception\MethodNotImplementedException;

abstract class AbstractRestController
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

        $result = $this->{$method}($request);

        if ($result instanceof Response) {
            return $result;
        } elseif (is_array($result)) {
            return new Response(json_encode($result));
        } else {
            throw new RuntimeException('Unhandled response type from controller');
        }
    }
}
