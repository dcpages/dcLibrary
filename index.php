<?php

/*
 * Exclude this file from coding standards, since we need to define constants
 * somewhere, and I think the side-effects rule is okay to break in this one
 * case.
 *
 * @codingStandardsIgnoreFile
 */
defined('WEBDIR') or define('WEBDIR', realpath(__DIR__));
defined('APPDIR') or define('APPDIR', realpath(WEBDIR.'/../application'));
defined('TMPDIR') or define('TMPDIR', '/tmp');

require APPDIR.'/bootstrap.php';
