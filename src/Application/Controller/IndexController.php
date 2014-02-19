<?php

namespace Application\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class IndexController
{
    use \Synapse\Controller\OAuthControllerTrait;

    public function indexAction(Request $request)
    {
        if ($this->isAuthenticated($request)) {
            return new Response('You are authenticated!');
        }

        return new Response('hello');
    }
}
