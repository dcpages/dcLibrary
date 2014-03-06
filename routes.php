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

if ($app['debug'] === false) {
    $app->error(function (\Exception $e, $code) {
        return new Symfony\Component\HttpFoundation\Response('Something went wrong with your request');
    });
}
