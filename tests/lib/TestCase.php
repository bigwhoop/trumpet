<?php

namespace Bigwhoop\Trumpet\Tests;

class TestCase extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        /** @var \DI\Container $container */
        $container = require __DIR__.'/../../app/etc/di.php';
        $container->set('WorkingDirectory', __DIR__ . '/../assets');
        $container->injectOn($this);
    }

    /**
     * @param string $expected
     * @param string $actual
     */
    protected function assertSourceCode($expected, $actual)
    {
        $this->assertEquals(str_replace("\r", '', $expected), str_replace("\r", '', $actual));
    }

    /**
     * @param string $text
     * @param string $indent
     *
     * @return string
     */
    protected function indentText($text, $indent = '    ')
    {
        $lines = explode("\n", str_replace("\r", '', $text));
        $lines = array_map(function ($line) use ($indent) {
            return $indent.$line;
        }, $lines);

        return  implode("\n", $lines);
    }
}
