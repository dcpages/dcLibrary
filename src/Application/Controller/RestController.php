<?php

namespace Application\Controller;

use Symfony\Component\HttpFoundation\Response;
use Synapse\Controller\AbstractRestController;
use Synapse\View\AbstractView;

class RestController extends AbstractRestController
{
    public function get()
    {
        return new Response(json_encode(array('hello' => 'world')));
    }

    public function post()
    {
        return new Response();
    }

    public function put()
    {
        return new Response();
    }

    public function delete()
    {
        return new Response();
    }
}
