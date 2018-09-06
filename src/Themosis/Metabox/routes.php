<?php

Route::apiResource('metabox', 'MetaboxApiController')->only([
    'show'
]);
