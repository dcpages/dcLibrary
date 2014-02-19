<?php

namespace Application\Controller;

use Symfony\Component\HttpFoundation\Response;

class IndexController
{
    protected $testView;

    public function __construct($testView)
    {
        $this->testView = $testView;
    }

    public function indexAction()
    {
        return new Response($this->testView);
    }
}
