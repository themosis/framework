<?php

define('DS', DIRECTORY_SEPARATOR);

define('THEMOSIS_PUBLIC_DIR', 'public');
define('THEMOSIS_ROOT', realpath(__DIR__.'/application'));
define('CONTENT_DIR', 'content');
define('WP_CONTENT_DIR', realpath(THEMOSIS_ROOT . DS . THEMOSIS_PUBLIC_DIR . DS . CONTENT_DIR));

if (file_exists($autoload = realpath(THEMOSIS_ROOT . '/../../vendor/autoload.php'))) {
    require $autoload;
}
