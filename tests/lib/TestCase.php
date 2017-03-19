<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Tests;

use DI\Container;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /** @var Container */
    private static $container;
    
    public static function setContainer(Container $container)
    {
        self::$container = $container;
    }
    
    public function setUp()
    {
        self::$container->injectOn($this);
    }

    protected function assertSourceCode(string $expected, string $actual)
    {
        $this->assertEquals(str_replace("\r", '', $expected), str_replace("\r", '', $actual));
    }

    protected function indentText(string $text, string $indent = '    '): string
    {
        $lines = explode("\n", str_replace("\r", '', $text));
        $lines = array_map(function ($line) use ($indent) {
            return $indent.$line;
        }, $lines);

        return  implode("\n", $lines);
    }
}
