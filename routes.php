<?php

$app->get('/', 'index.controller:indexAction');
$app->match('/rest', 'rest.controller:rest');

$app->error(function (\Synapse\Rest\Exception\MethodNotImplementedException $e, $code) {
    $response = new Symfony\Component\HttpFoundation\Response('Method not implemented');
    $response->setStatusCode(501);
    return $response;
});

$app->error(function (\Exception $e, $code) {
    return new Symfony\Component\HttpFoundation\Response('Something went wrong with your request');
});
