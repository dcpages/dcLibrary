<?php

namespace Application\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class IndexController
{
    use \Synapse\Controller\OAuthControllerTrait;

    protected $testView;

    public function __construct($testView)
    {
        $this->testView = $testView;
    }

    public function indexAction(Request $request)
    {
        if ($this->isAuthenticated($request)) {
            return new Response('You are authenticated!');
        }

        return new Response($this->testView);
    }
}
