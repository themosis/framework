<?php

/**
 * Perform any code before running the tests.
 */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

$loader = require_once(dirname(__FILE__).'/../vendor/autoload.php');
$loader->add('Themosis\\', dirname(__FILE__).'/../src/');

