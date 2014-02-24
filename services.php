<?php

// Register service providers
$app->register(new Synapse\Provider\ConsoleServiceProvider());
$app->register(new Synapse\Provider\ZendDbServiceProvider());
$app->register(new Synapse\Provider\RestControllerServiceProvider());
$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app->register(new Mustache\Silex\Provider\MustacheServiceProvider, array(
    'mustache.path' => APPDIR.'/templates',
    'mustache.options' => array(
        'cache' => TMPDIR,
    ),
));

$app->register(new Synapse\Provider\MigrationUpgradeServiceProvider());

// Register controllers and other shared services
$app['index.controller'] = $app->share(function () use ($app) {
    return new \Application\Controller\IndexController(
        new \Application\View\Test($app['mustache'])
    );
});

$app['rest.controller'] = $app->share(function () use ($app) {
    return new \Application\Controller\RestController;
});

// Register CLI commands
$app['test.command'] = $app->share(function () use ($app) {
    return new \Application\Command\TestCommand;
});
