<?php

// Register service providers
$app->register(new Synapse\Provider\ConsoleServiceProvider());
$app->register(new Synapse\Provider\ZendDbServiceProvider());
$app->register(new Synapse\Provider\OAuth2ServerServiceProvider());
$app->register(new Synapse\Provider\OAuth2SecurityServiceProvider());
$app->register(new Synapse\Provider\ResqueServiceProvider());
$app->register(new Synapse\Provider\UserServiceProvider());
$app->register(new Synapse\Provider\ControllerServiceProvider());

$app->register(new Mustache\Silex\Provider\MustacheServiceProvider, [
    'mustache.path' => APPDIR.'/templates',
    'mustache.options' => [
        'cache' => TMPDIR,
    ],
]);

$app->register(new Synapse\Provider\MigrationUpgradeServiceProvider());

// Register controllers and other shared services
$app['index.controller'] = $app->share(function () use ($app) {
    $index = new \Application\Controller\IndexController(
        new \Application\View\Test($app['mustache'])
    );

    $index->user = $app->user();

    return $index;
});

$app['rest.controller'] = $app->share(function () use ($app) {
    return new \Application\Controller\RestController;
});

// Register CLI commands
$app['test.command'] = $app->share(function () use ($app) {
    return new \Application\Command\TestCommand;
});

$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'unsecured' => array(
            'pattern'   => '^/oauth',
            'anonymous' => true,
        ),
        'api' => array(
            'pattern'   => '^/',
            'oauth'     => true,
            'stateless' => true,
        ),
    ),
));
