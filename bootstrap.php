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

// Create the application object
$app = new Silex\Application;

// Create routes
require_once APPDIR.'/routes.php';

$app->register(new Mustache\Silex\Provider\MustacheServiceProvider, array(
    'mustache.path' => APPDIR.'/templates',
    'mustache.options' => array(
        'cache' => TMPDIR,
    ),
));

$app->run();
