<?php

defined('DS') or die('No direct script access.');

/*----------------------------------------------------*/
// Register framework view paths.
/*----------------------------------------------------*/
add_filter('themosisViewPaths', function ($paths) {
    $paths[] = themosis_path('sys').'Metabox'.DS.'Views'.DS;
    $paths[] = themosis_path('sys').'Page'.DS.'Views'.DS;
    $paths[] = themosis_path('sys').'PostType'.DS.'Views'.DS;
    $paths[] = themosis_path('sys').'Field'.DS.'Fields'.DS.'Views'.DS;
    $paths[] = themosis_path('sys').'Route'.DS.'Views'.DS;
    $paths[] = themosis_path('sys').'User'.DS.'Views'.DS;

    return $paths;
});