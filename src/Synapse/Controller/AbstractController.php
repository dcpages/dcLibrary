<?php

namespace Synapse\Controller;

use Synapse\Application\UrlGeneratorAwareInterface;
use Synapse\Application\UrlGeneratorAwareTrait;

use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController implements UrlGeneratorAwareInterface
{
    use UrlGeneratorAwareTrait;

    public function createNotFoundResponse()
    {
        $response = new Response();
        $response->setStatusCode(404);
        $response->setContent('Not found');
        return $response;
    }
}
