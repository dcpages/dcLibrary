<?php

$app->command('test.command');

$app->get('/', 'index.controller:indexAction');
$app->match('/rest', 'rest.controller:rest');

$app->get('/private', 'private.controller:adminAction')
    ->secure(['ROLE_ADMIN']);

$app->error(function (\Synapse\Rest\Exception\MethodNotImplementedException $e, $code) {
    $response = new Symfony\Component\HttpFoundation\Response('Method not implemented');
    $response->setStatusCode(501);
    return $response;
});


$app->error(function (\Exception $e, $code) use ($app) {
    $app['log']->addError($e->getMessage(), ['exception' => $e]);

    if ($app['debug'] === false) {
        return new Symfony\Component\HttpFoundation\Response('Something went wrong with your request');
    } else {
        throw $e;
    }
});
