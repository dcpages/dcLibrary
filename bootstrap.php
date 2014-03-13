<?php

// Require the composer autoloader
require_once APPDIR.'/vendor/autoload.php';

// Autoload the rest of our application
spl_autoload_register(function ($className) {
    $className = ltrim($className, '\\');
    $fileName  = '';
    $namespace = '';

    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }

    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    if (file_exists(APPDIR.'/src/'.$fileName)) {
        require APPDIR.'/src/'.$fileName;
    }
});

// Set the default time zone.
date_default_timezone_set('UTC');

// Initialize the Silex Application
$applicationInitializer = new Synapse\ApplicationInitializer;

$app = $applicationInitializer->initialize();

// Set the default routes and services
$defaultRoutes   = new Synapse\Application\Routes;
$defaultServices = new Synapse\Application\Services;

$defaultRoutes->define($app);
$defaultServices->register($app);

// Set the application-specific routes and services
$appRoutes   = new Application\Routes;
$appServices = new Application\Services;

$appRoutes->define($app);
$appServices->register($app);

// Run the application
$app->run();
