<?php

namespace Themosis\Tests\Core;

use PHPUnit\Framework\TestCase;
use Themosis\Core\EnvironmentDetector;

class EnvironmentDetectorTest extends TestCase
{
    public function testClosureCanBeUsedForEnvironmentDetection()
    {
        $env = new EnvironmentDetector();

        $result = $env->detect(function () {
            return 'foo';
        });

        $this->assertEquals('foo', $result);
    }

    public function testConsoleEnvironmentDetection()
    {
        $env = new EnvironmentDetector();

        $result = $env->detect(function () {
            return 'goo';
        }, ['--env=local']);

        $this->assertEquals('local', $result);
    }
}
