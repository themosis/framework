<?php

namespace Themosis\Field;

use Themosis\Foundation\ServiceProvider;

class FieldServiceProvider extends ServiceProvider
{
    protected $provides = [
        'field'
    ];

    public function register()
    {
        $this->getContainer()->share('field', 'Themosis\Field\Factory')->withArgument('view');
    }
}
