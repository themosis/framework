<?php

define('DS', DIRECTORY_SEPARATOR);

define('THEMOSIS_PUBLIC_DIR', 'public');
define('THEMOSIS_ROOT', realpath(__DIR__ . '/application'));

if (file_exists($autoload = realpath(THEMOSIS_ROOT . '/../../vendor/autoload.php'))) {
    require $autoload;
}
