<?php

return [
    'autoloading' => [
        'Com\\Themosis\\Plugin\\' => 'resources'
    ],
    'providers' => [
        Com\Themosis\Plugin\Providers\Route::class
    ],
    'views' => [
        'views'
    ],
    'anyvar' => true
];
