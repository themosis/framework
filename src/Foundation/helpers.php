<?php

if (!function_exists('web_path')) {
    function web_path($path = '')
    {
        return app()->webPath($path);
    }
}