<?php

namespace Application\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class IndexController
{
    use \Silex\Application\SecurityTrait;

    protected $testView;

    public function __construct($testView)
    {
        $this->testView = $testView;
    }

    public function indexAction(Request $request)
    {
        return new Response($this->testView);
    }
}
