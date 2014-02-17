<?php

$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Synapse\Provider\RestControllerServiceProvider());

$app['index.controller'] = $app->share(function () use ($app) {
    return new \Application\Controller\IndexController;
});

$app->get('/', 'index.controller:indexAction');

$app['rest.controller'] = $app->share(function () use ($app) {
    return new \Application\Controller\RestController;
});

$app->match('/rest', 'rest.controller:rest');

$app->error(function (\Synapse\Rest\Exception\MethodNotImplementedException $e, $code) {
    $response = new Symfony\Component\HttpFoundation\Response('Method not implemented');
    $response->setStatusCode(501);
    return $response;
});

$app->error(function (\Exception $e, $code) {
    return new Symfony\Component\HttpFoundation\Response('Something went wrong with your request');
});
