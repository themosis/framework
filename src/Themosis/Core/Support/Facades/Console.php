<?php

namespace Themosis\Core\Support\Facades;

use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Support\Facades\Facade;

/**
 * Class Console
 *
 * @method static int handle(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output = null)
 * @method static int call(string $command, array $parameters = [], $outputBuffer = null)
 * @method static int queue(string $command, array $parameters = [])
 * @method static array all()
 * @method static string output()
 *
 * @package Themosis\Core\Support\Facades
 *
 * @see \Illuminate\Contracts\Console\Kernel
 */
class Console extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ConsoleKernelContract::class;
    }
}
