<?php

namespace Application\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class PrivateController
{
    protected $testView;

    public function __construct($testView)
    {
        $this->testView = $testView;
    }

    public function adminAction(Request $request)
    {
        return new Response('You have access');
    }
}
