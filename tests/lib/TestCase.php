<?php

namespace Bigwhoop\Trumpet\Tests;

class TestCase extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        /** @var \DI\Container $container */
        $container = require __DIR__.'/../../app/etc/di.php';
        $container->injectOn($this);
    }
}
