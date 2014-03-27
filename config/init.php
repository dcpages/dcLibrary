<?php

/**
 * Application version
 * Follows semantic versioning
 *
 * @link http://semver.org/
 *
 * @var string
 */
$appVersion = '0.0.0';

return [
    'version'    => $appVersion,
    'debug'      => false,
    'migrations' => 'Application\Migrations\\',
    'upgrades'   => 'Application\Upgrades\\',
];
